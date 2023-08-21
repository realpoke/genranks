<?php

namespace App\Console\Commands;

use App\Models\GameUser;
use App\Models\User;
use Illuminate\Console\Command;

class RemoveUsersWithoutVerifiedGames extends Command
{
    protected $signature = 'remove:users';

    protected $description = 'Remove users without verified games';

    public function handle()
    {
        $this->info('Removing users without verified games...');

        $userIdsToRemove = User::where('claimed_at', null)
            ->whereDoesntHave('games', function ($query) {
                $query->where('verifications', '>', 1);
            })
            ->pluck('id');

        // Detach all games from users before deleting the users
        GameUser::whereIn('user_id', $userIdsToRemove)->delete();

        User::whereIn('id', $userIdsToRemove)->delete();

        $this->info('Users without verified games removal complete.');
    }
}
