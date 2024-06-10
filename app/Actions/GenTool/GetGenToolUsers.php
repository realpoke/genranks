<?php

namespace App\Actions\GenTool;

use App\Contracts\GenTool\GetsGenToolUsersContract;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GetGenToolUsers implements GetsGenToolUsersContract
{
    protected const BASE_URL = 'https://www.gentool.net/data/zh';

    public function __invoke(CarbonInterface $day): Collection
    {
        return $this->getUserURLS($this->combineURL($day));
    }

    private function combineURL(CarbonInterface $day): string
    {
        return $this::BASE_URL.'/'.$day->format('Y_m_F/d_l');
    }

    private function getUserURLs(string $fullURL): Collection
    {
        try {
            $response = Http::get($fullURL);
        } catch (\Illuminate\Http\Client\ConnectionException $ex) {
            return collect();
        }

        $lines = explode(PHP_EOL, $response->getBody());
        $users = collect();
        foreach ($lines as $line) {
            $link = $this->stringBetween($line, 'src="/icons/folder-icon.png" alt="[DIR]"></td><td><a href="', '/">');
            if ($link == '') {
                continue;
            }

            $nickname = $this->stringBetween($line, $link.'/">', '/</a></td><td align="right">');
            $users->put($nickname, $fullURL.'/'.$link);
        }

        return $users;
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
