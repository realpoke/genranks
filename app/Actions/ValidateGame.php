<?php

namespace App\Actions;

use App\Contracts\ValidatesGameContract;
use App\Enums\GameStatus;
use App\Enums\Side;
use App\Factories\WinnerProcessorFactory;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class ValidateGame implements ValidatesGameContract
{
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
        if ($game->type->validPlayerCount(count($game->players)) === false) {
            Log::debug('Game type: '.$game->type->name.' - Invalid player count ('.count($game->players).'). Game not valid');

            return GameStatus::INVALID;
        }

        // If any player is not on a valid side, the game is invalid
        foreach ($game->players as $player) {
            if (! Side::isValidSide($player['Side'])) {
                Log::debug('Invalid side: '.$player['Side'].'. Game not valid');

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
        $processor = WinnerProcessorFactory::getProcessor($game->type);

        return $processor($game);
    }
}
