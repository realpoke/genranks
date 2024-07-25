<?php

namespace App\Actions\WinnerProcessor;

use App\Contracts\Factory\WinnerProcessorContract;
use App\Enums\Army;
use App\Enums\GameStatus;
use App\Jobs\GiveUserStats;
use App\Jobs\UpdateArmy;
use App\Jobs\UpdateEloAndRank;
use App\Models\Game;
use App\Models\Matchup;
use Illuminate\Support\Facades\Log;

class FreeForAllWinnerProcessor implements WinnerProcessorContract
{
    public function __invoke(Game $game): GameStatus
    {
        Log::debug('Free for all winner processor');

        $users = $game->users;

        // Collect surrender times for each user
        $surrenderTimes = [];
        foreach ($users as $user) {
            $commands = $user->pivot->commands;
            if ($commands) {
                $playerSurrenders = [];
                foreach ($commands as $command) {
                    if ($command['OrderName'] === 'Surrender') {
                        $playerName = $command['PlayerName'];
                        $timeCode = $command['TimeCode'];
                        if (! isset($playerSurrenders[$playerName]) || $timeCode < $playerSurrenders[$playerName]) {
                            $playerSurrenders[$playerName] = $timeCode;
                        }
                    }
                }
                // Use the earliest surrender time for this user
                if (! empty($playerSurrenders)) {
                    $surrenderTimes[$user->id] = min($playerSurrenders);
                }
            }
        }

        // Check if all users except one have surrendered
        if (count($surrenderTimes) < count($users) - 1) {
            Log::warning('Not all users except one have surrendered. Game is invalid.');

            return GameStatus::INVALID;
        }

        // Sort users by surrender time
        asort($surrenderTimes);

        // Update elimination order and set winner
        $eliminationOrder = 1;
        $lastUserId = array_key_last($surrenderTimes);
        foreach ($surrenderTimes as $userId => $time) {
            $updateData = ['ffa_elimination_order' => $eliminationOrder];
            if ($userId === $lastUserId) {
                $updateData['summary'] = ['Win' => true];
            }
            $game->users()->updateExistingPivot($userId, $updateData);
            $eliminationOrder++;
        }

        // Set the winner (the user who didn't surrender)
        $winnerId = $users->pluck('id')->diff(array_keys($surrenderTimes))->first();
        if ($winnerId) {
            $game->users()->updateExistingPivot($winnerId, [
                'ffa_elimination_order' => $eliminationOrder,
                'summary' => array_merge($game->users()->find($winnerId)->pivot->summary, ['Win' => true]),
            ]);
        }

        if ($game->map?->ranked) {
            UpdateEloAndRank::dispatch($game)->onQueue('sequential');
            GiveUserStats::dispatch($game);
            $this->processFFAMatchupAndDispatch($game);

            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            $this->logMapDetails($game, 'Map already in pool and ranked');

            return GameStatus::VALID;
        } else {
            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            $this->logMapDetails($game, 'Map could be added');

            return GameStatus::UNRANKED;
        }
    }

    private function processFFAMatchupAndDispatch(Game $game)
    {
        $users = $game->users;

        // Collect army and placement information
        $playerInfo = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'army' => Army::from($user->pivot->summary['Side']),
                'placement' => $user->pivot->ffa_elimination_order,
            ];
        });

        // Get the total number of players
        $totalPlayers = $playerInfo->count();

        // Create a matchup entry for each player
        foreach ($playerInfo as $player) {
            $opponents = $playerInfo->where('id', '!=', $player['id'])->pluck('army')->toArray();

            // Calculate score based on placement
            $score = ($totalPlayers - $player['placement']) - ($player['placement'] - 1);

            // Dispatch the UpdateArmy job
            UpdateArmy::dispatch(
                $player['army'],
                $opponents,
                $game->type,
                $score
            )->onQueue('sequential');
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
