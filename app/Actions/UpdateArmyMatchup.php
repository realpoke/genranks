<?php

namespace App\Actions;

use App\Contracts\UpdatesArmyMatchupContract;
use App\Enums\Army;
use App\Enums\GameType;
use App\Models\Matchup;

class UpdateArmyMatchup implements UpdatesArmyMatchupContract
{
    public function __invoke(Army $winningArmy, Army $opponentArmy, GameType $gameType): void
    {
        $matchup = Matchup::where(function ($query) use ($winningArmy) {
            $query->where('army', $winningArmy)->orWhere('opponent', $winningArmy);
        })->where('game_type', $gameType)->first();

        if (is_null($matchup)) {
            $matchup = Matchup::create([
                'army' => $winningArmy,
                'opponent' => $opponentArmy,
                'game_type' => $gameType,
                'score' => 0,
            ]);

            return;
        }

        if ($matchup->army === $winningArmy) {
            $matchup->increment('score');
        } else {
            $matchup->decrement('score');
        }

        $matchup->save();
    }
}
