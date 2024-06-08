<?php

namespace App\Actions;

use App\Contracts\CalculatesElo;
use App\Models\User;
use Illuminate\Support\Collection;

class CalculateElo implements CalculatesElo
{
    private int $kFactor = 32;

    public function __invoke(User $playerA, User $playerB, bool $playerAWon): Collection
    {
        $expectedA = 1 / (1 + pow(10, ($playerB->elo - $playerA->elo) / 400));
        $expectedB = 1 / (1 + pow(10, ($playerA->elo - $playerB->elo) / 400));

        $newRatingA = $playerA->elo + $this->kFactor * (($playerAWon ? 1 : 0) - $expectedA);
        $newRatingB = $playerB->elo + $this->kFactor * (($playerAWon ? 0 : 1) - $expectedB);

        return collect([
            'playerANewElo' => round($newRatingA),
            'playerBNewElo' => round($newRatingB),
        ]);
    }
}
