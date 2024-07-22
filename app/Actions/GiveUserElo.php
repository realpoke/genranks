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
        // We refresh to get the up to date elo_change values
        $game->refresh();
        try {
            DB::transaction(function () use ($game) {
                foreach ($game->users as $user) {
                    if (is_null($user->pivot->elo_change)) {
                        throw new Exception('Elo change field is null for user: '.$user->id.' in game: '.$game->id);
                    }

                    foreach (EloRankType::values() as $rankType) {
                        Log::debug('Changing elo for rank type: '.$rankType);
                        $success = $user->pivot->summary['Win']
                            ? $user->giveElo(
                                $user->pivot->elo_change,
                                game: null,
                                rankType: EloRankType::from($rankType),
                                gameType: $game->type
                            )
                            : $user->takeElo(
                                $user->pivot->elo_change,
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
