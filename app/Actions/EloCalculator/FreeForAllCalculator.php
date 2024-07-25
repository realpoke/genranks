<?php

namespace App\Actions\EloCalculator;

use App\Contracts\Factory\EloCalculatorContract;
use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\Game;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FreeForAllCalculator implements EloCalculatorContract
{
    private int $kFactor = 32;

    public function __invoke(Game $game): bool
    {
        Log::debug('Free For All calculator');
        $eloField = EloRankType::ALL->databaseEloField(GameType::FREE_FOR_ALL_EIGHT);

        Log::debug('Elo field: '.$eloField);

        $users = $game->users->sortByDesc(function ($user) {
            return $user->pivot->ffa_elimination_order;
        })->values();

        if ($users->count() < 3) {
            Log::error('Not enough players for Free For All game: '.$game->id);

            return false;
        }

        try {
            DB::transaction(function () use ($game, $users, $eloField) {
                // Lock rows for all users in the game
                $userIds = $users->pluck('id');
                DB::table('game_user')
                    ->whereIn('user_id', $userIds)
                    ->where('game_id', $game->id)
                    ->lockForUpdate()
                    ->get();

                $eloChanges = $this->calculateEloChanges($users, $eloField);

                // Update elo_change for all users
                foreach ($eloChanges as $userId => $eloChange) {
                    $game->users()->updateExistingPivot($userId, ['elo_change' => $eloChange]);
                }
            }, 3);
        } catch (Exception $e) {
            Log::error('Elo calculation transaction failed: '.$e->getMessage());

            return false; // Transaction failed
        }

        Log::debug('Elo updated for Free For All game');

        return true; // Transaction succeeded
    }

    private function calculateEloChanges($users, $eloField): array
    {
        $eloChanges = array_fill_keys($users->pluck('id')->all(), 0);
        $matchupsCount = $users->count() - 1; // Number of matchups for each player

        for ($i = 0; $i < $users->count(); $i++) {
            for ($j = $i + 1; $j < $users->count(); $j++) {
                $winner = $users[$i];
                $loser = $users[$j];

                $expectedWinner = $this->getExpectedScore($winner->$eloField, $loser->$eloField);
                $expectedLoser = 1 - $expectedWinner;

                $winnerEloChange = $this->kFactor * (1 - $expectedWinner);
                $loserEloChange = $this->kFactor * (0 - $expectedLoser);

                $eloChanges[$winner->id] += $winnerEloChange;
                $eloChanges[$loser->id] += $loserEloChange;
            }
        }

        // Average out the Elo changes
        foreach ($eloChanges as $userId => $change) {
            $eloChanges[$userId] = $change / $matchupsCount;
        }

        // Round the final Elo changes
        return array_map('round', $eloChanges);
    }

    private function getExpectedScore($playerElo, $opponentElo): float
    {
        return 1 / (1 + pow(10, ($opponentElo - $playerElo) / 400));
    }
}
