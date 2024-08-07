<?php

namespace App\Factories;

use App\Actions\WinnerProcessor\FreeForAllWinnerProcessor;
use App\Actions\WinnerProcessor\OneOnOneWinnerProcessor;
use App\Actions\WinnerProcessor\TeamWinnerProcessor;
use App\Contracts\Factory\WinnerProcessorContract;
use App\Enums\GameStatus;
use App\Enums\GameType;

class WinnerProcessorFactory
{
    public static function getProcessor(GameType $gameType): WinnerProcessorContract
    {
        return match ($gameType) {
            GameType::ONE_ON_ONE => new OneOnOneWinnerProcessor(),
            GameType::TWO_ON_TWO,
            GameType::THREE_ON_THREE,
            GameType::FOUR_ON_FOUR => new TeamWinnerProcessor(),
            GameType::FREE_FOR_ALL_THREE,
            GameType::FREE_FOR_ALL_FOUR,
            GameType::FREE_FOR_ALL_FIVE,
            GameType::FREE_FOR_ALL_SIX,
            GameType::FREE_FOR_ALL_SEVEN,
            GameType::FREE_FOR_ALL_EIGHT => new FreeForAllWinnerProcessor(),
            default => fn () => GameStatus::INVALID,
        };
    }
}
