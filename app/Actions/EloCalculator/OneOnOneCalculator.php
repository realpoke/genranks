<?php

namespace App\Actions\EloCalculator;

use App\Contracts\Factory\EloCalculatorContract;
use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\Game;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OneOnOneCalculator implements EloCalculatorContract
{
    private int $kFactor = 32;

    public function __invoke(Game $game): bool
    {
        Log::debug('One on One calculator');
        $eloField = EloRankType::ALL->databaseEloField(GameType::ONE_ON_ONE);

        Log::debug('Elo field: '.$eloField);

        $winner = $game->users->first()->pivot->summary['Win'] ? $game->users->first() : $game->users->last();
        $loser = $game->users->last()->pivot->summary['Win'] ? $game->users->first() : $game->users->last();

        Log::debug('Winner: '.$winner->id);
        Log::debug('Loser: '.$loser->id);

        $expectedWinner = 1 / (1 + pow(10, ($loser->$eloField - $winner->$eloField) / 400));
        $expectedLoser = 1 / (1 + pow(10, ($winner->$eloField - $loser->$eloField) / 400));

        $newRatingWinner = $winner->$eloField + $this->kFactor * (1 - $expectedWinner);
        $newRatingLoser = $loser->$eloField + $this->kFactor * (0 - $expectedLoser);

        $winnerEloChange = round($newRatingWinner - $winner->$eloField);
        $loserEloChange = round($newRatingLoser - $loser->$eloField);

        Log::debug('Winner Elo Change: '.$winnerEloChange);
        Log::debug('Loser Elo Change: '.$loserEloChange);

        try {
            Log::debug('Updating Elo');
            DB::transaction(function () use ($game, $winner, $loser, $winnerEloChange, $loserEloChange) {
                $game->users()->updateExistingPivot($winner->id, ['elo_change' => $winnerEloChange]);
                $game->users()->updateExistingPivot($loser->id, ['elo_change' => $loserEloChange]);
            }, 3);
        } catch (Exception $e) {
            Log::error('Elo calculation transaction failed: '.$e->getMessage());

            return false; // Transaction failed
        }
        Log::debug('Elo updated');

        return true; // Transaction succeeded
    }
}
