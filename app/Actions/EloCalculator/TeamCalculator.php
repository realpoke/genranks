<?php

namespace App\Actions\EloCalculator;

use App\Contracts\Factory\EloCalculatorContract;
use App\Enums\EloRankType;
use App\Enums\GameType;
use App\Models\Game;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamCalculator implements EloCalculatorContract
{
    private int $kFactor = 32;

    public function __invoke(Game $game): bool
    {
        Log::debug('Team calculator');
        $eloField = EloRankType::ALL->databaseEloField(GameType::TWO_ON_TWO);

        Log::debug('Elo field: '.$eloField);

        // Order users same as game players field
        $users = $game->users->sortBy(function ($user) {
            return $user->pivot->header['ArrayReplayOwnerSlot'];
        })->values();

        $gamePlayers = collect($game->players);

        if ($users->count() !== $gamePlayers->count()) {
            Log::error('Mismatch in number of users and game players for game: '.$game->id);

            return false;
        }

        $teams = $users->zip($gamePlayers)->groupBy(function ($pair) {
            return $pair[1]['Team'];
        })->filter(function ($team) {
            return $team->count() > 0;
        });

        if ($teams->count() !== 2) {
            Log::error('Invalid number of teams for game: '.$game->id);

            return false;
        }

        // Plucking to just get the users
        $team1 = $teams->first()->pluck(0);
        $team2 = $teams->last()->pluck(0);

        $team1Won = $team1->every(function ($user) {
            return $user->pivot->summary['Win'] ?? false;
        });
        $team2Won = $team2->every(function ($user) {
            return $user->pivot->summary['Win'] ?? false;
        });

        if (! $team1Won && ! $team2Won) {
            Log::error('No team won for game: '.$game->id);

            return false;
        }

        if ($team1Won === $team2Won) {
            Log::error('Teams are tied for game: '.$game->id);

            return false;
        }

        $team1AverageElo = $team1->avg($eloField);
        $team2AverageElo = $team2->avg($eloField);

        Log::debug('Team 1 Average Elo: '.$team1AverageElo);
        Log::debug('Team 2 Average Elo: '.$team2AverageElo);

        $winningTeam = $team1Won ? $team1 : $team2;
        $losingTeam = $team2Won ? $team1 : $team2;
        $winningTeamAvgElo = $team1Won ? $team1AverageElo : $team2AverageElo;
        $losingTeamAvgElo = $team2Won ? $team1AverageElo : $team2AverageElo;

        // Calculate expected scores
        $expectedWinner = 1 / (1 + pow(10, ($losingTeamAvgElo - $winningTeamAvgElo) / 400));
        $expectedLoser = 1 / (1 + pow(10, ($winningTeamAvgElo - $losingTeamAvgElo) / 400));

        // Calculate Elo changes
        $winningTeamEloChange = round($this->kFactor * (1 - $expectedWinner));
        $losingTeamEloChange = round($this->kFactor * (0 - $expectedLoser));

        Log::debug('Winning Team Elo Change: '.$winningTeamEloChange);
        Log::debug('Losing Team Elo Change: '.$losingTeamEloChange);

        try {
            Log::debug('Updating Elo');
            DB::transaction(function () use ($game, $winningTeam, $losingTeam, $winningTeamEloChange, $losingTeamEloChange) {
                // Lock rows for the users in both teams and the game
                $userIds = $winningTeam->pluck('id')->merge($losingTeam->pluck('id'))->unique();
                DB::table('game_user')
                    ->whereIn('user_id', $userIds)
                    ->where('game_id', $game->id)
                    ->lockForUpdate()
                    ->get(); // Acquire locks for the rows

                foreach ($winningTeam as $user) {
                    $game->users()->updateExistingPivot($user->id, ['elo_change' => $winningTeamEloChange]);
                }
                foreach ($losingTeam as $user) {
                    $game->users()->updateExistingPivot($user->id, ['elo_change' => $losingTeamEloChange]);
                }
            }, 3);
        } catch (Exception $e) {
            Log::error('Elo calculation transaction failed: '.$e->getMessage());

            return false; // Transaction failed
        }
        Log::debug('Elo updated');

        return true; // Transaction succeeded
    }
}
