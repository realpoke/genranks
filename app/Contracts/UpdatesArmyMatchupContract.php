<?php

namespace App\Contracts;

use App\Enums\Army;
use App\Enums\GameType;

interface UpdatesArmyMatchupContract
{
    public function __invoke(Army|array $armies, Army|array $opponents, GameType $gameType, ?int $score = null): void;
}
