<?php

namespace App\Actions;

use App\Contracts\Gentool\GetsUsersContract;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GetUsers implements GetsUsersContract
{
    protected const BASE_URL = 'https://www.gentool.net/data/zh';

    public function users(CarbonInterface $day): \Illuminate\Support\Collection
    {
        $fullURL = $this->combineURL($day);

        return $this->getUserURLS($fullURL);
    }

    private function combineURL(CarbonInterface $day): string
    {
        return $this::BASE_URL.'/'.$day->format('Y_m_F/d_l');
    }

    private function getUserURLs(string $fullURL): \Illuminate\Support\Collection
    {
        try {
            $response = Http::get($fullURL);
        } catch (\Illuminate\Http\Client\ConnectionException $ex) {
            return collect();
        }
        $lines = explode(PHP_EOL, $response->getBody());
        $users = collect();
        foreach ($lines as $line) {
            $link = Str::between($line, 'src="/icons/folder-icon.png" alt="[DIR]"></td><td><a href="', '/">');
            if ($link == '') {
                continue;
            }

            $nickname = Str::between($line, $link.'/">', '/</a></td><td align="right">');
            $users->put($nickname, $fullURL.'/'.$link);
        }

        return $users;
    }
}
