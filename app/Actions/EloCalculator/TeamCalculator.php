<?php

namespace App\Actions\EloCalculator\EloCalculator;

use App\Contracts\Factory\EloCalculatorContract;
use App\Models\Game;

class TeamCalculator implements EloCalculatorContract
{
    public function __invoke(Game $game): bool
    {
        return false;
    }
}
