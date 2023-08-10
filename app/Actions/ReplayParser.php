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

        if (Storage::disk('binaries')->missing('replay_parser')) {
            Log::error('Did not find binary file: replay_parser!');

            return collect();
        }

        $replay = Storage::disk('replays')->path($file);
        if (Storage::disk('binaries')->exists('replay_parser_live')) {
            $binary = Storage::disk('binaries')->path('replay_parser_live');
        } else {
            $binary = Storage::disk('binaries')->path('replay_parser');
        }

        $processResult = Process::run([$binary, $replay]);

        if (! $processResult->successful()) {
            Log::error($processResult->errorOutput());

            return collect();
        }

        // TODO: Fix going over allowed memory allocation size in php
        return collect(json_decode($processResult->output(), true));
    }
}
