<?php

namespace App\Actions\GenTool;

use App\Contracts\GenTool\GetsValidGenToolGamesContract;
use App\Enums\GameType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GetValidGenToolGames implements GetsValidGenToolGamesContract
{
    protected const BASE_URL = 'https://www.gentool.net/data/zh';

    public function __invoke(string $userURL): Collection
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
            if (in_array(explode('_', $matchName)[1], GameType::validGenToolGameType()) === false) {
                continue;
            }

            $games->put($matchName, $userURL.'/'.$matchName.'.rep');
        }

        return $games;
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
