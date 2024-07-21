<?php

namespace App\Contracts;

use App\Enums\Army;
use App\Enums\GameType;

interface UpdatesArmyMatchupContract
{
    public function __invoke(Army|array $army, Army|array $opponent, GameType $gameType): void;
}
