<?php

namespace App\Actions;

use App\Contracts\UpdatesArmyMatchupContract;
use App\Enums\GameType;
use App\Models\Matchup;

class UpdateArmyMatchup implements UpdatesArmyMatchupContract
{
    public function __invoke(array $winningArmies, array $losingArmies, GameType $gameType): void
    {
        $this->updateMatchup($winningArmies, $losingArmies, $gameType);
    }

    private function updateMatchup(array $winningArmies, array $losingArmies, GameType $gameType): void
    {
        $matchup = Matchup::where(function ($query) use ($winningArmies, $losingArmies) {
            $query->where(function ($q) use ($winningArmies, $losingArmies) {
                $q->whereJsonHas('armies', $winningArmies)
                    ->whereJsonHas('opponents', $losingArmies);
            })->orWhere(function ($q) use ($winningArmies, $losingArmies) {
                $q->whereJsonHas('armies', $losingArmies)
                    ->whereJsonHas('opponents', $winningArmies);
            });
        })->where('game_type', $gameType)->first();

        if (is_null($matchup)) {
            Matchup::create([
                'armies' => $winningArmies,
                'opponents' => $losingArmies,
                'game_type' => $gameType,
                'score' => 1,
            ]);

            return;
        }

        if ($matchup->armies === $winningArmies) {
            $matchup->increment('score');
        } else {
            $matchup->decrement('score');
        }

        $matchup->save();
    }
}
