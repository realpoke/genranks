<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ParsesReplayContract
{
    public function __invoke(string $replayFile): Collection;
}
