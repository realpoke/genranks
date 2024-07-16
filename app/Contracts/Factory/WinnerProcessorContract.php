<?php

namespace App\Contracts\Factory;

use App\Enums\GameStatus;
use App\Models\Game;

interface WinnerProcessorContract
{
    public function __invoke(Game $game): GameStatus;
}
