<?php

namespace App\Actions;

use App\Contracts\GivesUserEloContract;
use App\Models\Game;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiveUserElo implements GivesUserEloContract
{
    public function __invoke(Game $game): bool
    {
        try {
            return DB::transaction(function () use ($game) {
                foreach ($game->users as $user) {
                    if (is_null($user->pivot->elo_change)) {
                        throw new Exception('Elo change field is null for user: '.$user->id.' in game: '.$game->id);
                    }

                    $success = $user->pivot->summary['Win']
                        ? $user->giveElo(
                            $user->pivot->elo_change,
                            game: null,
                            rankType: $game->rank_type,
                            gameType: $game->type
                        )
                        : $user->takeElo(
                            $user->pivot->elo_change,
                            game: null,
                            rankType: $game->rank_type,
                            gameType: $game->type
                        );

                    if ($success === false) {
                        throw new Exception('Failed to give/take elo for user: '.$user->id.' in game: '.$game->id);
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
