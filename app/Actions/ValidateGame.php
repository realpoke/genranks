<?php

namespace App\Actions;

use App\Contracts\ValidatesGameContract;
use App\Enums\GameStatus;
use App\Jobs\GiveUserStats;
use App\Jobs\UpdateEloAndRank;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class ValidateGame implements ValidatesGameContract
{
    public function __invoke(Game $game): bool
    {
        if ($game->status != GameStatus::VALIDATING) {
            return $game->update(['status' => GameStatus::INVALID]); // We are not validating, we should not be here
        }
        $checkStatus = $this->validCheck($game);

        return $game->update(['status' => $checkStatus]); // Set valid to valid check
    }

    private function validCheck(Game $game): GameStatus
    {
        // TODO: Chcek map is in the pool of validated maps

        // Check there are two players and they don't have a team
        if (count($game->players) != 2) { // Two players
            return GameStatus::INVALID; // Not exactly two players. Game not valid
        }
        foreach ($game->players as $player) {
            if ($player['Type'] != 'H') { // Humans
                return GameStatus::INVALID; // None-human players. Game not valid
            }
        }

        // Check both player replays are uploaded
        if ($game->users->count() != 2) {
            return GameStatus::INVALID;
        }

        return $this->processWinner($game);
    }

    private function processWinner(Game $game): GameStatus
    {
        $playerA = $game->summary[0];
        $playerB = $game->summary[1];

        $playerAWon = $playerA['Win'];
        $playerBWon = $playerB['Win'];

        if ($playerAWon === false && $playerBWon === false) {
            return GameStatus::DRAW;
        }

        if ($playerAWon === true && $playerBWon === true) {
            return GameStatus::INVALID;
        }

        foreach ($game->users as $user) {
            $replayOwnerSlot = $user->pivot->header['ArrayReplayOwnerSlot'];

            if ($replayOwnerSlot === 0) {
                // Match found for Player A
                $playerAUser = $user;
            } elseif ($replayOwnerSlot === 1) {
                // Match found for Player B
                $playerBUser = $user;
            }
        }

        // Ensure both players are found before dispatching the job
        if ($playerAUser && $playerBUser) {
            UpdateEloAndRank::dispatch($playerAUser, $playerBUser, $playerAWon, $game)->onQueue('sequential');
            GiveUserStats::dispatch($game);

            return GameStatus::VALID;
        }

        // If players are not found
        Log::error('Players not found for game: '.$game->id);

        return GameStatus::INVALID;
    }
}
