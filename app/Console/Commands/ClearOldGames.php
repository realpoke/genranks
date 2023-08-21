<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\GameUser;
use Illuminate\Console\Command;

class ClearOldGames extends Command
{
    protected $signature = 'game:clear';

    protected $description = 'Delete 1 day old games without verification.';

    public function handle()
    {
        $this->info('Clearing old games...');

        $oldGames = Game::notVerified()->where('updated_at', '<=', now()->addDays(-1));
        $oldGamesCount = $oldGames->count();

        // Detach all users from old games before deleting the games
        GameUser::whereIn('game_id', $oldGames->pluck('id'))->delete();

        $oldGames->delete();

        $this->info('Cleared '.$oldGamesCount.' old games!');
    }
}
