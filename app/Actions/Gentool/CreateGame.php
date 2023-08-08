<?php

namespace App\Actions\Gentool;

use App\Actions\ReplayParser;
use App\Contracts\Gentool\CreatesGameContract;
use App\Contracts\ReplaysParserContract;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Helper\ProgressBar;

class CreateGame implements CreatesGameContract
{
    protected const BASE_URL = 'https://www.gentool.net/data/zh';

    protected CreatePlaceholderUser $userCreator;

    public function __construct(CreatePlaceholderUser $userCreator)
    {
        $this->userCreator = $userCreator;
    }

    public function create(
        Collection $users,
        ProgressBar $progress = null
    ) {
        $updateProgressTime = Carbon::now()->addMinute();
        foreach ($users as $nickname => $userURL) {
            $progress->setMessage('Processing: '.$nickname.' | calculating game(s)');
            $user = $this->userCreator->create($nickname);
            $gameLinks = $this->getGameLinks($userURL);
            $progress->setMessage('Processing: '.$nickname.' | '.$gameLinks->count().' game(s)');

            foreach ($gameLinks as $gameName => $gameURL) {
                $game = $this->createGame($gameURL.'.rep');

                if (is_null($game)) {
                    continue;
                }
                $this->attachUserToGame($user, $game);
            }

            $progress->advance();
            if (Carbon::now()->isAfter($updateProgressTime)) {
                $updateProgressTime = Carbon::now()->addMinute();
                Cache::put('gentool_fetch_command_progress', (int) ($progress->getProgressPercent() * 100));
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
            $matchName = $this->stringBetween($line, 'trophy.png" alt="[REP]"></td><td><a href="', '.rep">');
            if ($matchName == '') {
                continue;
            }

            $games->put($matchName, $userURL.'/'.$matchName);
        }

        return $games;
    }

    private function createGame(
        string $gameReplayLink,
        ReplaysParserContract $parser = new ReplayParser()
    ): ?Game {
        $replay = file_get_contents($gameReplayLink);
        $replayName = time().'_temp_replay.rep';

        Storage::disk('replays')->put($replayName, $replay);
        $data = $parser->parse($replayName);
        Storage::disk('replays')->delete($replayName);

        if (
            $data->isEmpty() ||
            $data['Header']['Metadata']['Players'] == null ||
            $data['Summary'] == null ||
            count($data['Body']) <= 50 // Don't count games with less than 50 actions
        ) {
            return null;
        }
        $gameHash = $this->createGameHash($data);

        return Game::firstOrCreate(['hash' => $gameHash],
            [
                'data' => $data->except('Body'),
                'hash' => $gameHash,
            ]);
    }

    private function createGameHash(Collection $data): string
    {
        return md5($data['Header']['NumTimeStamps'].$data['Header']['Metadata']['MapSize'].$data['Body'][50]['TimeCode'].$data['Header']['Hash']).
            md5($data['Header']['Hash'].$data['Header']['Metadata']['Seed']);
    }

    private function attachUserToGame(User $user, Game $game)
    {
        if (
            $game->verifications >= count($game->data['Header']['Metadata']['Players']) ||
            $game->users->contains($user)
        ) {
            return false;
        }

        // TODO: Check if user is winner, and set winner in pivot
        // TODO: Add user stats in the user_game pivot
        $game->users()->attach($user);
        $game->increment('verifications');

        if ($game->verifications >= count($game->data['Header']['Metadata']['Players'])) {
            // TODO: Calculate elo for game
        }
    }

    private function stringBetween(string $string, string $start, string $end): string
    {
        $string = ' '.$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }
}
