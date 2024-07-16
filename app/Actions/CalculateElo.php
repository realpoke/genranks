<?php

namespace App\Actions;

use App\Contracts\CalculatesEloContract;
use App\Enums\EloRankType;
use App\Models\User;
use Illuminate\Support\Collection;

class CalculateElo implements CalculatesEloContract
{
    private int $kFactor = 32;

    public function __invoke(User $playerA, User $playerB, bool $playerAWon, EloRankType $rankType = EloRankType::ALL): Collection
    {
        $eloField = $rankType->databaseEloField();

        $expectedA = 1 / (1 + pow(10, ($playerB->$eloField - $playerA->$eloField) / 400));
        $expectedB = 1 / (1 + pow(10, ($playerA->$eloField - $playerB->$eloField) / 400));

        $newRatingA = $playerA->$eloField + $this->kFactor * (($playerAWon ? 1 : 0) - $expectedA);
        $newRatingB = $playerB->$eloField + $this->kFactor * (($playerAWon ? 0 : 1) - $expectedB);

        return collect([
            'playerANewElo' => round($newRatingA),
            'playerAChangedElo' => round($newRatingA - $playerA->$eloField),

            'playerBNewElo' => round($newRatingB),
            'playerBChangedElo' => round($newRatingB - $playerB->$eloField),
        ]);
    }
}
