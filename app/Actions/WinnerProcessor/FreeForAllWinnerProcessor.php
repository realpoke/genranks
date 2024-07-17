<?php

namespace App\Actions\WinnerProcessor;

use App\Contracts\Factory\WinnerProcessorContract;
use App\Enums\GameStatus;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class FreeForAllWinnerProcessor implements WinnerProcessorContract
{
    public function __invoke(Game $game): GameStatus
    {
        Log::debug('Free for all winner processor');

        // TODO: Calculate elo as many 1v1 games
        // When the first player loses, then its like losing a 1v1 against all the other players.
        // When the second player loses, then its like losing a 1v1 against all the remaining players.
        // ETC
        // Then divide it by the number of 1v1s simulated to normalize it.

        return GameStatus::INVALID;
    }
}
