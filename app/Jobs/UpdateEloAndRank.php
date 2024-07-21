<?php

namespace App\Jobs;

use App\Contracts\GivesUserEloContract;
use App\Factories\EloCalculatorFactory;
use App\Models\Game;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
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

    public function handle(GivesUserEloContract $eloGiver)
    {
        // TODO: Check if we need to do this refreash
        $this->game->refresh();

        $eloCalculator = EloCalculatorFactory::getProcessor($this->game);

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
    }
}
