<?php

namespace App\Console\Commands;

use App\Enums\GameStatus;
use App\Models\Game;
use Illuminate\Console\Command;

class ValidateGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This goes over all games in need of validation and tries to validate them.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $games = Game::where('status', GameStatus::VALIDATING)->get();

        foreach ($games as $game) {
            $game->validate();
        }
    }
}
