<?php

namespace App\Contracts;

use App\Enums\Army;
use App\Enums\GameType;

interface UpdatesArmyMatchupContract
{
    public function __invoke(Army $army, Army $opponent, GameType $gameType): void;
}
