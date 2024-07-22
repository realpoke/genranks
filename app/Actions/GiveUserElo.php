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
        // TODO: Make a system that resets weekly and monthly ladder
        // System should create and give a badge to users in the temporary ladders

        // We refresh to get the up to date elo_change values
        // TODO: Seems like this is not enought, users don't get their correct ranks, maybe use lockForUpdate?
        // TODO: Allow admins to use the artisan command "ranks:calibrate" to recalibrate all users
        // NOTE: Trying to fix with WithoutOverlapping on the job
        try {
            DB::transaction(function () use ($game) {
                // Lock the game record
                $game = Game::where('id', $game->id)->lockForUpdate()->first();

                foreach ($game->users as $user) {
                    // Re-fetch the pivot data within the transaction to ensure it's up-to-date
                    // And lock the pivot data for update
                    $pivotData = DB::table('game_user')
                        ->where('game_id', $game->id)
                        ->where('user_id', $user->id)
                        ->lockForUpdate()
                        ->first();

                    if (is_null($pivotData->elo_change)) {
                        throw new Exception('Elo change field is null for user: '.$user->id.' in game: '.$game->id);
                    }

                    foreach (EloRankType::values() as $rankType) {
                        Log::debug('Changing elo for rank type: '.$rankType);
                        $success = $pivotData->summary['Win']
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
                            throw new Exception('Failed to give/take elo for user: '.$user->id.' in game: '.$game->id);
                        }
                    }
                }
            });
        } catch (Exception $e) {
            Log::error('Elo and rank transaction failed: '.$e->getMessage());

            return false; // Transaction failed
        }

        return true; // Transaction succeeded
    }
}
