<?php

namespace App\Jobs;

use App\Contracts\GivesUserEloContract;
use App\Factories\EloCalculatorFactory;
use App\Models\Game;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateEloAndRank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function middleware()
    {
        return [new WithoutOverlapping];
    }

    public function handle(GivesUserEloContract $eloGiver)
    {
        Log::debug('Update Elo and Rank');
        // TODO: Check if we need to do this refreash
        $this->game->refresh();

        Log::debug('Game ID: '.$this->game->id);

        $eloCalculator = EloCalculatorFactory::getProcessor($this->game);

        Log::debug('Calculating Elo');
        Log::debug('Game type: '.$this->game->type->name);
        if (! $eloCalculator($this->game)) {
            Log::error('Failed to calculate elo for game: '.$this->game->id);

            return;
        }

        if (! $eloGiver($this->game)) {
            Log::error('Failed to give elo/take for game: '.$this->game->id);
            $this->game->users->each(function ($user) {
                $user->pivot->elo_change = 0;
                $user->save();
            });

            return;
        }
        Log::debug('Elo calculated');
    }
}
