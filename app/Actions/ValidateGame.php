<?php

namespace App\Actions;

use App\Contracts\ValidatesGameContract;
use App\Enums\GameStatus;
use App\Jobs\UpdateEloAndRank;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class ValidateGame implements ValidatesGameContract
{
    public function __invoke(Game $game): bool
    {
        if ($game->status != GameStatus::VALIDATING) {
            Log::debug('Status was not validating, we should not be here.');

            return $game->update(['status' => GameStatus::INVALID]); // We are not validating, we should not be here
        }
        $checkStatus = $this->validCheck($game);

        return $game->update(['status' => $checkStatus]); // Set valid to valid check
    }

    private function validCheck(Game $game): GameStatus
    {
        Log::debug($game);
        Log::debug($game->players);
        Log::debug($game->users);

        // Check there are two players and they don't have a team
        if (count($game->players) != 2) { // Two players
            Log::debug('Not exactly two players. Game not valid');

            return GameStatus::INVALID; // Not exactly two players. Game not valid
        }
        foreach ($game->players as $player) {
            if ($player['Type'] != 'H' || $player['Team'] != '-1') { // Humans and no team
                Log::debug('None-human players. Game not valid');

                return GameStatus::INVALID; // None-human players. Game not valid
            }
        }

        // Check both player replays are uploaded
        if ($game->users->count() != 2) {
            Log::debug('Not two users attached');

            return GameStatus::INVALID;
        }

        return $this->processWinner($game);
    }

    private function processWinner(Game $game): GameStatus
    {
        Log::debug('Processing winner');
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

        UpdateEloAndRank::dispatch($playerA, $playerB, $playerAWon)->onQueue('sequential');

        return GameStatus::CALCULATING;
    }
}
