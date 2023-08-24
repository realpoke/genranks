<?php

namespace App\Actions;

use App\Contracts\ReplaysParserContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ReplayParser implements ReplaysParserContract
{
    public function parse(string $file): Collection
    {
        if (Storage::disk('replays')->missing($file)) {
            Log::error('Did not find replay file: '.$file);

            return collect();
        }

        $replay = Storage::disk('replays')->path($file);
        $binary = $this->getReplayParserBinary();

        if ($binary === false) {
            Log::error('Did not find replay parser!');

            return collect();
        }

        $processResult = Process::env([
            'PATH' => '/usr/local/bin:/usr/bin:/bin',
        ])->run([$binary, $replay]);

        if (! $processResult->successful()) {
            Log::error($processResult->errorOutput());

            return collect();
        }

        // Decode JSON in smaller chunks
        // Change the 2 in the line below to how big in mb the chunks can be
        $jsonChunks = str_split($processResult->output(), 2 * 1024 * 1024); // Split into 2 MB chunks
        $decodedData = collect();

        foreach ($jsonChunks as $chunk) {
            $decodedChunk = json_decode($chunk, true, 512, JSON_BIGINT_AS_STRING);
            if ($decodedChunk !== null) {
                $decodedData = $decodedData->merge($decodedChunk);
            } else {
                Log::error('Failed to decode JSON chunk: '.json_last_error_msg());
            }
        }

        return $decodedData;
    }

    private function getReplayParserBinary(): string|bool
    {
        if (strtoupper(PHP_OS) === 'WINNT') {
            if (Storage::disk('binaries')->exists('replay_parser_live.exe')) {
                return Storage::disk('binaries')->path('replay_parser_live.exe');
            } elseif (Storage::disk('binaries')->exists('replay_parser.exe')) {
                return Storage::disk('binaries')->path('replay_parser.exe');
            }
        } else {
            if (Storage::disk('binaries')->exists('replay_parser_live')) {
                return Storage::disk('binaries')->path('replay_parser_live');
            } elseif (Storage::disk('binaries')->exists('replay_parser')) {
                return Storage::disk('binaries')->path('replay_parser');
            }
        }

        return false;
    }
}
