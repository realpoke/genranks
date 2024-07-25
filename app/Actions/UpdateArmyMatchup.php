<?php

namespace App\Actions;

use App\Contracts\UpdatesArmyMatchupContract;
use App\Enums\Army;
use App\Enums\GameType;
use App\Models\Matchup;
use Illuminate\Support\Facades\Log;

class UpdateArmyMatchup implements UpdatesArmyMatchupContract
{
    public function __invoke(Army|array $winningArmies, Army|array $losingArmies, GameType $gameType, ?int $score = null): void
    {
        if (! is_array($winningArmies)) {
            $winningArmies = [$winningArmies];
        }
        if (! is_array($losingArmies)) {
            $losingArmies = [$losingArmies];
        }

        $this->updateMatchup($winningArmies, $losingArmies, $gameType, $score);
    }

    private function updateMatchup(array $winningArmies, array $losingArmies, GameType $gameType, ?int $score = null): void
    {
        if (empty($winningArmies) || empty($losingArmies)) {
            Log::error('Both winning and losing armies must be provided');

            return;
        }

        foreach ($winningArmies as $winningArmy) {
            if (! ($winningArmy instanceof Army)) {
                Log::error('Invalid winning army: '.$winningArmy);

                return;
            }
        }

        foreach ($losingArmies as $losingArmy) {
            if (! ($losingArmy instanceof Army)) {
                Log::error('Invalid losing army: '.$losingArmy);

                return;
            }
        }

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
                'score' => $score ?? 1,
            ]);

            return;
        }

        if ($score != null) {
            $matchup->increment('score', $score);
        } elseif ($matchup->armies === $winningArmies) {
            $matchup->increment('score');
        } else {
            $matchup->decrement('score');
        }

        $matchup->save();
    }
}
