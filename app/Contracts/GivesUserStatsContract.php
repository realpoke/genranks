<?php

namespace App\Contracts;

use App\Models\Game;

interface GivesUserStatsContract
{
    public function __invoke(Game $game): void;
}
