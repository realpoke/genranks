<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class ClearGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:clear {--cleanusers : Whether to clean users after deleting games}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old invalid games and optionally cleans users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cleanUsers = $this->option('cleanusers');

        $oldGames = Game::failed()->where('updated_at', '<=', Carbon::now()->subHour());

        $oldGames->delete();

        $this->info('Old games cleanup completed.');

        if ($cleanUsers) {
            $this->info('Also clearing old unused users.');
            Artisan::call('user:clear');
        }
    }
}
