<?php

namespace App\Actions;

use App\Contracts\ValidatesGameContract;
use App\Enums\GameStatus;
use App\Enums\RankMode;
use App\Enums\Side;
use App\Factories\WinnerProcessorFactory;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class ValidateGame implements ValidatesGameContract
{
    private const MAX_ELO_DIFFERENCE_FOR_BALANCED = 500;

    public function __invoke(Game $game): bool
    {
        if ($game->status != GameStatus::VALIDATING) {
            Log::debug('Game not validating, skipping validation');

            return $game->update(['status' => GameStatus::INVALID]);
        }
        $checkStatus = $this->validCheck($game);

        return $game->update(['status' => $checkStatus]);
    }

    private function validCheck(Game $game): GameStatus
    {
        // Check if there are enough players
        if ($game->type->validPlayerCount(count($game->players)) === false) {
            Log::debug('Game type: '.$game->type->name.' - Invalid player count ('.count($game->players).'). Game not valid');

            return GameStatus::INVALID;
        }

        // If any player is not on a valid side, the game is invalid
        foreach ($game->users as $user) {
            if (! Side::isValidSide($user['pivot']['summary']['Side'])) {
                Log::debug('Invalid side: '.$user['pivot']['summary']['Side'].'. Game not valid');

                return GameStatus::INVALID;
            }
        }

        Log::debug('Game Players:');
        Log::debug(collect($game->players));
        foreach ($game->players as $player) {
            if ($player['Type'] != 'H') { // H = Humans
                Log::debug('None-human players. Game not valid');

                return GameStatus::INVALID;
            }
        }

        if ($game->users->count() < $game->type->replaysNeededForValidation()) {
            Log::debug('At least '.$game->type->replaysNeededForValidation().' player replays must be uploaded. Game not valid');

            return GameStatus::INVALID;
        }

        return $this->processWinner($game);
    }

    private function processWinner(Game $game): GameStatus
    {
        // Check users ranked mode
        $balanced = false;
        foreach ($game->users as $user) {
            if ($user->rank_mode == RankMode::ALL) {
                continue;
            } elseif ($user->rank_mode == RankMode::FUN) {
                Log::debug('User playing for fun, unranked: '.$user->id);

                return GameStatus::UNRANKED;
            } elseif ($user->rank_mode == RankMode::BALANCED) {
                Log::debug('User playing balanced, checking elo difference: '.$user->id);

                $balanced = true;
            }
        }
        if ($balanced === true) {
            Log::debug('Game needs to be balanced, checking elo difference');

            if ($this->checkBalanced($game) === GameStatus::UNRANKED) {
                return GameStatus::UNRANKED;
            }
        }

        $processor = WinnerProcessorFactory::getProcessor($game->type);

        return $processor($game);
    }

    private function checkBalanced(Game $game): GameStatus
    {
        // Get the difference between the highest and lowest elo for all users in the game
        $userElos = $game->users->pluck('elo')->toArray();

        $highestElo = max($userElos);
        $lowestElo = min($userElos);
        $eloDifference = $highestElo - $lowestElo;

        if ($eloDifference > self::MAX_ELO_DIFFERENCE_FOR_BALANCED) {
            Log::debug('Elo difference too high, game not balanced, max allowed: '.self::MAX_ELO_DIFFERENCE_FOR_BALANCED.' elo difference: '.$eloDifference);

            return GameStatus::INVALID;
        }

        return GameStatus::VALID;
    }
}
