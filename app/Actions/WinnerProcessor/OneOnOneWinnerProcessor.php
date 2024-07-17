<?php

namespace App\Actions\WinnerProcessor;

use App\Contracts\Factory\WinnerProcessorContract;
use App\Enums\GameStatus;
use App\Jobs\GiveUserStats;
use App\Jobs\UpdateEloAndRank;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class OneOnOneWinnerProcessor implements WinnerProcessorContract
{
    public function __invoke(Game $game): GameStatus
    {
        Log::debug('One on One winner processor');

        $playerAUser = null;
        $playerBUser = null;

        // Set the 'win' value in the pivot table and assign players
        foreach ($game->users as $user) {
            $replayOwnerSlot = $user->pivot->header['ArrayReplayOwnerSlot'];
            $user->pivot->win = $game->summary[$replayOwnerSlot]['Win'];
            $user->pivot->save();

            if ($replayOwnerSlot === 0) {
                $playerAUser = $user;
            } elseif ($replayOwnerSlot === 1) {
                $playerBUser = $user;
            }
        }

        if (! $playerAUser || ! $playerBUser) {
            Log::error('Players not found for game: '.$game->id);

            return GameStatus::INVALID;
        }

        $playerAWon = $playerAUser->pivot->win;
        $playerBWon = $playerBUser->pivot->win;

        if ($playerAWon === false && $playerBWon === false) {
            return GameStatus::DRAW;
        }

        if ($playerAWon === true && $playerBWon === true) {
            Log::debug('Both players won. Game not valid'); // TODO: If this is the case in 1v1, then check if we can find a surrender command

            return GameStatus::INVALID;
        }

        Log::debug('Map ranked: '.($game->map?->ranked));
        if ($game->map?->ranked) {
            UpdateEloAndRank::dispatch($playerAUser, $playerBUser, $playerAWon, $game)->onQueue('sequential');
            GiveUserStats::dispatch($game); // TODO: Think about if we should give stats for none-ranked games

            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            Log::debug('Map already in pool and ranked: '.$game->meta['MapHash']);
            Log::debug('MapFile: '.$game->meta['MapFile']);
            Log::debug('MapCRC: '.$game->meta['MapCRC']);
            Log::debug('MapSize: '.$game->meta['MapSize']);

            return GameStatus::VALID;
        } else {
            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            Log::debug('Map could be added: '.$game->meta['MapHash']);
            Log::debug('MapFile: '.$game->meta['MapFile']);
            Log::debug('MapCRC: '.$game->meta['MapCRC']);
            Log::debug('MapSize: '.$game->meta['MapSize']);

            return GameStatus::UNRANKED;
        }
    }
}
