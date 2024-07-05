<?php

namespace App\Actions\GenTool;

use App\Contracts\GenTool\CreatesGenToolUserContract;
use App\Models\User;
use Illuminate\Support\Collection;

class CreateGenToolUser implements CreatesGenToolUserContract
{
    public function __invoke(string ...$nicknames): Collection
    {
        return collect($nicknames)->map(function ($nickname): User {
            $gentoolId = collect(explode('_', $nickname))->last();
            $existingUser = User::whereJsonContains('gentool_ids', $gentoolId)->first();

            if ($existingUser) {
                return $existingUser;
            }

            return User::Create(
                [
                    'name' => $nickname,
                    'gentool_ids' => [$gentoolId],
                    'email' => $nickname.fake()->randomNumber(5, true).'@'.fake()->safeEmailDomain(),
                    'password' => bcrypt(fake()->password),
                    'fake' => true,
                ]
            );
        });
    }
}
