<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface CalculatesEloContract
{
    public function __invoke(User $playerA, User $playerB, bool $playerAWon): Collection;
}
