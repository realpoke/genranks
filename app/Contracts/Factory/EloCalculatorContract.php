<?php

namespace App\Contracts\Factory;

use App\Models\Game;

interface EloCalculatorContract
{
    public function __invoke(Game $game): bool;
}
