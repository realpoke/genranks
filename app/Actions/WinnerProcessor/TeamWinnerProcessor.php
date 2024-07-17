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

        // TODO: Combine all team members elo into one for both teams
        // then do a normal elo calculation on that like its 1v1
        // Then take the lost and gained elo and divide it by the number of players on each team

        return GameStatus::INVALID;
    }
}
