<?php

namespace App\Actions;

use App\Contracts\Gentool\CreatesPlaceholderUserContract;
use App\Models\User;

class CreatePlaceholderUser implements CreatesPlaceholderUserContract
{
    protected const BASE_URL = 'https://www.gentool.net/data/zh';

    public function create($nickname)
    {
        return User::firstOrCreate(
            ['nickname' => $nickname],
            [
                'nickname' => $nickname,
                'email' => $nickname.fake()->randomNumber(5, true).'@'.fake()->domainName(),
                'password' => 'password',
            ]
        );
    }
}
