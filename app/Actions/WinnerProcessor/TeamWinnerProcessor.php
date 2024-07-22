<?php

namespace App\Actions\WinnerProcessor;

use App\Contracts\Factory\WinnerProcessorContract;
use App\Enums\GameStatus;
use App\Jobs\GiveUserStats;
use App\Models\Game;
use Log;

class TeamWinnerProcessor implements WinnerProcessorContract
{
    public function __invoke(Game $game): GameStatus
    {
        Log::debug('Team winner processor');

        // Order users same as game players field
        $users = $game->users->sortBy(function ($user) {
            return $user->pivot->header['ArrayReplayOwnerSlot'];
        })->values();
        Log::debug('Users: '.collect($users));
        Log::debug('Users count: '.collect($users)->count());

        $gamePlayers = collect($game->players);
        Log::debug('Game players: '.collect($gamePlayers));
        Log::debug('Players count: '.collect($gamePlayers)->count());

        if ($users->count() !== $gamePlayers->count()) {
            Log::error('Mismatch in number of users and game players for game: '.$game->id);

            return GameStatus::INVALID;
        }

        $teams = $users->zip($gamePlayers)->groupBy(function ($pair) {
            return $pair[1]['Team'];
        })->filter(function ($team) {
            return $team->count() > 0;
        });
        Log::debug('Teams: '.collect($teams));

        if ($teams->count() !== 2) {
            Log::error('Invalid number of teams for game: '.$game->id);

            return GameStatus::INVALID;
        }

        // Plucking to just get the users
        $team1 = $teams->first()->pluck(0);
        $team2 = $teams->last()->pluck(0);
        Log::debug('Team1: '.collect($team1));
        Log::debug('Team2: '.collect($team2));

        $team1Won = $team1->every(function ($user) {
            return $user->pivot->summary['Win'] ?? false;
        });
        $team2Won = $team2->every(function ($user) {
            return $user->pivot->summary['Win'] ?? false;
        });
        Log::debug('Team1 Won: '.$team1Won);
        Log::debug('Team2 Won: '.$team2Won);

        if (! $team1Won && ! $team2Won) {
            return GameStatus::DRAW; // TODO: This should not happen, we should remove draw status
        }

        if ($team1Won && $team2Won) {
            Log::debug('Both teams won. Game not valid');

            return GameStatus::INVALID;
        }

        if ($game->map?->ranked) {
            Log::debug('Ranked game');
            Log::debug('Team1 won: '.$team1Won);
            Log::debug('Team2 won: '.$team2Won);

            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            $this->logMapDetails($game, 'Map already in pool and ranked');

            // TODO: Calculate elo and update ranks / elo for all users in the game
            GiveUserStats::dispatch($game);

            // Create arrays of armies for each team
            $team1Armies = $team1->map(function ($user) {
                return Army::from($user->pivot->summary['Side']);
            })->toArray();
            $team2Armies = $team2->map(function ($user) {
                return Army::from($user->pivot->summary['Side']);
            })->toArray();
            UpdateArmy::dispatch(
                $team1Won ? $team1Armies : $team2Armies,
                $team1Won ? $team2Armies : $team1Armies,
                $game->type
            );

            return GameStatus::VALID;
        } else {
            Log::debug('Unranked game');

            // TODO: Remove when we have enough maps in the pool and added to the database seeder
            $this->logMapDetails($game, 'Map could be added');

            return GameStatus::UNRANKED;
        }

        return GameStatus::INVALID;
    }

    private function logMapDetails(Game $game, string $message): void
    {
        Log::debug($message.': '.$game->meta['MapHash']);
        Log::debug('MapFile: '.$game->meta['MapFile']);
        Log::debug('MapCRC: '.$game->meta['MapCRC']);
        Log::debug('MapSize: '.$game->meta['MapSize']);
    }
}
