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

        return GameStatus::INVALID;
    }
}
