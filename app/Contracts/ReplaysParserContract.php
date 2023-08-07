<?php

namespace App\Contracts;

interface ReplaysParserContract
{
    public function parse(string $replayFile): string;
}
