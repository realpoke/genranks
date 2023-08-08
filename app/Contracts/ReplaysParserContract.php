<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ReplaysParserContract
{
    public function parse(string $replayFile): Collection;
}
