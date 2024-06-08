<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface CalculatesElo
{
    public function __invoke(User $playerA, User $playerB, bool $playerAWon): Collection;
}
