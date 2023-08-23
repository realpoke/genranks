<?php

namespace App\Contracts;

use App\Models\Game;
use Illuminate\Support\Collection;

interface GetsGameWinnerContract
{
    public function winner(Game $game): Collection;
}
