<?php

namespace App\Actions;

use App\Contracts\ReplaysParserContract;

class ReplayParser implements ReplaysParserContract
{
    public function parse(string $replayFile)
    {
        // TODO: Parse replay file data.
        return 'test';
    }
}
