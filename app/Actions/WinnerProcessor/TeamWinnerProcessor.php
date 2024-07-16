<?php

namespace App\Actions\WinnerProcessor;

use App\Contracts\Factory\WinnerProcessorContract;
use App\Enums\GameStatus;
use App\Models\Game;
use Log;

class TeamWinnerProcessor implements WinnerProcessorContract
{
    public function __invoke(Game $game): GameStatus
    {
        Log::debug('Team winner processor');

        return GameStatus::INVALID;
    }
}
