<?php

namespace App\Actions\Gentool;

use App\Contracts\Gentool\CreatesPlaceholderUserContract;
use App\Models\User;

class CreatePlaceholderUser implements CreatesPlaceholderUserContract
{
    public function create($nickname): User
    {
        return User::firstOrCreate(
            ['nickname' => $nickname],
            [
                'name' => $nickname,
                'nickname' => $nickname,
                'email' => $nickname.fake()->randomNumber(5, true).'@'.fake()->domainName(),
                'password' => 'password',
                'claimed_at' => null,
            ]
        );
    }
}
