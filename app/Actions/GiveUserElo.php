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
        Log::debug('Giving Elo for game: '.$game->id);

        $game->refresh();
        try {
            return DB::transaction(function () use ($game) {
                foreach ($game->users as $user) {
                    $user->refresh();
                    $pivotData = $user->pivot;
                    Log::debug('Pivot data: '.$pivotData);

                    if (! is_int($pivotData->elo_change)) {
                        Log::error('Invalid Elo change for user: '.$user->id);
                        throw new Exception('Invalid Elo change for user: '.$user->id);
                    }

                    foreach (EloRankType::values() as $rankType) {
                        $eloChange = $pivotData->elo_change;
                        $eloMethod = $pivotData->summary['Win'] ? 'giveElo' : 'takeElo';

                        if (! $user->$eloMethod($eloChange, EloRankType::from($rankType), $game->type)) {
                            Log::error('Failed to '.$eloMethod.' Elo for user: '.$user->id);
                            throw new Exception('Failed to '.$eloMethod.' Elo for user: '.$user->id);
                        }
                    }
                }

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Elo calculation transaction failed: '.$e->getMessage());

            $game->users->each(function ($user) {
                $user->pivot->elo_change = 0;
                $user->save();
            });

            return false;
        }
    }
}
