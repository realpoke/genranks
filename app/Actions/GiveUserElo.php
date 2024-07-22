<?php

namespace App\Actions;

use App\Contracts\GivesUserEloContract;
use App\Enums\EloRankType;
use App\Models\Game;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiveUserElo implements GivesUserEloContract
{
    public function __invoke(Game $game): bool
    {
        try {
            DB::transaction(function () use ($game) {
                // Lock the game record
                $game = Game::where('id', $game->id)->lockForUpdate()->first();

                foreach ($game->users as $user) {
                    // Update Elo for each user in a separate transaction
                    $this->updateUserElo($user, $game);
                }
            });
        } catch (Exception $e) {
            Log::error('Elo and rank transaction failed: '.$e->getMessage());

            return false; // Transaction failed
        }

        return true; // Transaction succeeded
    }

    private function updateUserElo($user, Game $game): bool
    {
        return DB::transaction(function () use ($user, $game) {
            // Re-fetch the pivot data within the transaction to ensure it's up-to-date
            $pivotData = DB::table('game_user')
                ->where('game_id', $game->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (is_null($pivotData->elo_change)) {
                throw new Exception('Elo change field is null for user: '.$user->id.' in game: '.$game->id);
            }

            $summary = json_decode($pivotData->summary, true);

            foreach (EloRankType::values() as $rankType) {
                $success = $summary['Win']
                    ? $user->giveElo(
                        $pivotData->elo_change,
                        game: null,
                        rankType: EloRankType::from($rankType),
                        gameType: $game->type
                    )
                    : $user->takeElo(
                        $pivotData->elo_change,
                        game: null,
                        rankType: EloRankType::from($rankType),
                        gameType: $game->type
                    );

                if (! $success) {
                    Log::error('Failed to give/take elo for user: '.$user->id.' in game: '.$game->id);
                    throw new Exception('Failed to give/take elo for user: '.$user->id.' in game: '.$game->id);
                }
            }

            return true; // Transaction succeeded
        });
    }
}
