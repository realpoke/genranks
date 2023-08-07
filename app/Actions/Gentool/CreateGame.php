<?php

namespace App\Actions;

use App\Contracts\Gentool\CreatesGameContract;
use App\Contracts\Gentool\CreatesPlaceholderUserContract;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateGame implements CreatesGameContract
{
    protected const BASE_URL = 'https://www.gentool.net/data/zh';

    public function create(
        Collection $users,
        CreatesPlaceholderUserContract $userCreator
    ) {
        foreach ($users as $nickname => $userURL) {
            $user = $userCreator->create($nickname);
            $gameLinks = $this->getGameLinks($userURL);

            foreach ($gameLinks as $gameName => $gameURL) {
                $game = $this->createGame($gameURL.'.rep');
                try {
                    $this->attachUserToGame($user, $game);
                } catch (\Throwable $th) {
                    continue;
                }
            }
        }
    }

    private function getGameLinks(string $userURL): Collection
    {
        try {
            $response = Http::get($userURL);
        } catch (\Illuminate\Http\Client\ConnectionException $ex) {
            return collect();
        }
        $lines = explode(PHP_EOL, $response->getBody());
        $games = collect();
        foreach ($lines as $line) {
            $matchName = Str::between($line, 'trophy.png" alt="[REP]"></td><td><a href="', '.rep">');
            if ($matchName == '') {
                continue;
            }

            $games->put($matchName, $userURL.'/'.$matchName);
        }

        return $games;
    }

    private function createGame(string $gameReplayLink): Game
    {
        // TODO: Download temp replay from $gameReplayLink.
        $replay = file_get_contents($gameReplayLink);
        Storage::disk('replays')->put('temp-replay.rep', $replay);

        // TODO: Parse temp replay file.

        // TODO: Remove temp replay file.

        // TODO: Create game from parsed replay data and return the game.
    }

    private function attachUserToGame(User $user, Game $game)
    {
        if (
            $game->verifications >= $game->data->player_count ||
            $game->users->contains($user)
        ) {
            return false;
        }

        // TODO: Check if user is winner, and set winner in pivot

        // TODO: Verify every time we add a user
        $game->users()->attach($user);

        // TODO: Check if we have enough verifications and start calculating elo.
    }
}
