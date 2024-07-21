<?php

namespace App\Actions\EloCalculator;

use App\Contracts\Factory\EloCalculatorContract;
use App\Models\Game;

class FreeForAllCalculator implements EloCalculatorContract
{
    public function __invoke(Game $game): bool
    {
        return false;
    }
}
