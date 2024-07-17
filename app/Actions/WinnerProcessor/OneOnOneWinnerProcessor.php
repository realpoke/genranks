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

        $users = $game->users->sortBy(function ($user) {
            return $user->pivot->header['ArrayReplayOwnerSlot'];
        })->values();

        if ($users->count() !== 2) {
            Log::error('Incorrect number of players for game: '.$game->id);

            return GameStatus::INVALID;
        }

        $playerAUser = $users[0];
        $playerBUser = $users[1];

        $playerAWon = $playerAUser->pivot->summary['Win'] ?? false;
        $playerBWon = $playerBUser->pivot->summary['Win'] ?? false;

        if (! $playerAWon && ! $playerBWon) {
            return GameStatus::DRAW;
        }

        if ($playerAWon && $playerBWon) {
            Log::debug('Both players won. Game not valid');

            return GameStatus::INVALID;
        }

        Log::debug('Map ranked: '.($game->map?->ranked));
        if ($game->map?->ranked) {
            UpdateEloAndRank::dispatch($playerAUser, $playerBUser, $playerAWon, $game)->onQueue('sequential');
            GiveUserStats::dispatch($game);

            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            $this->logMapDetails($game, 'Map already in pool and ranked');

            return GameStatus::VALID;
        } else {
            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            $this->logMapDetails($game, 'Map could be added');

            return GameStatus::UNRANKED;
        }
    }

    private function logMapDetails(Game $game, string $message): void
    {
        Log::debug($message.': '.$game->meta['MapHash']);
        Log::debug('MapFile: '.$game->meta['MapFile']);
        Log::debug('MapCRC: '.$game->meta['MapCRC']);
        Log::debug('MapSize: '.$game->meta['MapSize']);
    }
}
