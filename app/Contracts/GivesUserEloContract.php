<?php

namespace App\Contracts;

use App\Models\Game;

interface GivesUserEloContract
{
    public function __invoke(Game $game): bool;
}
