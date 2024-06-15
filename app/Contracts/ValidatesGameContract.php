<?php

namespace App\Contracts;

use App\Models\Game;

interface ValidatesGameContract
{
    public function __invoke(Game $game): bool;
}
