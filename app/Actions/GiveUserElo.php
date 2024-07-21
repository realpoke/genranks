<?php

namespace App\Actions;

use App\Contracts\GivesUserEloContract;
use App\Models\Game;

class GiveUserElo implements GivesUserEloContract
{
    public function __invoke(Game $game): bool
    {
        foreach ($game->users as $user) {
            if (is_null($user->pivot->elo_change)) {
                return false;
            } elseif ($user->pivot->summary['Win']) {
                $success = $user->giveElo(
                    $user->pivot->elo_change,
                    game: null,
                    rankType: $game->rank_type,
                    gameType: $game->type
                );
            } else {
                $success = $user->takeElo(
                    $user->pivot->elo_change,
                    game: null,
                    rankType: $game->rank_type,
                    gameType: $game->type
                );
            }
            if ($success === false) {
                return false;
            }
        }

        return true;
    }
}
