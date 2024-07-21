<?php

namespace App\Factories;

use App\Actions\EloCalculator\FreeForAllCalculator;
use App\Actions\EloCalculator\OneOnOneCalculator;
use App\Actions\EloCalculator\TeamCalculator;
use App\Contracts\Factory\EloCalculatorContract;
use App\Enums\GameType;
use App\Models\Game;

class EloCalculatorFactory
{
    public static function getProcessor(Game $game): EloCalculatorContract
    {
        return match ($game->type) {
            GameType::ONE_ON_ONE => new OneOnOneCalculator(),
            GameType::TWO_ON_TWO,
            GameType::THREE_ON_THREE,
            GameType::FOUR_ON_FOUR => new TeamCalculator(),
            GameType::FREE_FOR_ALL_THREE,
            GameType::FREE_FOR_ALL_FOUR,
            GameType::FREE_FOR_ALL_FIVE,
            GameType::FREE_FOR_ALL_SIX,
            GameType::FREE_FOR_ALL_SEVEN,
            GameType::FREE_FOR_ALL_EIGHT => new FreeForAllCalculator(),
            default => fn () => false,
        };
    }
}
