<?php

namespace App\Actions;

use App\Contracts\ParsesReplayContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class ReplayParser implements ParsesReplayContract
{
    public function __invoke(string $file): Collection
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
            Log::error('Failed to parse with error: '.$processResult->errorOutput());

            return collect();
        }

        // TODO: decode in chunks to avoid memory size issues.
        try {
            $decodedData = collect(json_decode($processResult->output(), true, 512, JSON_BIGINT_AS_STRING));
        } catch (JsonException $e) {
            $decodedData = collect();
            Log::error('Failed to decode json for '.$replay.': '.$e->getMessage());
        }

        return $decodedData;
    }

    private function getReplayParserBinary(): string|bool
    {
        // Live parser is proprarity binaries, none-live is dummy binaries
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
