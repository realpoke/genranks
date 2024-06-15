<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ClearGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old replays, invalid games';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldGames = Game::failed()->where('updated_at', '<=', Carbon::now()->addDays(-1))->get();

        $oldGames->each->delete();

        $this->info('Old games cleanup completed.');
    }
}
