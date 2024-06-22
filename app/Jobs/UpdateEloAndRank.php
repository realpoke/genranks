<?php

namespace App\Jobs;

use App\Contracts\CalculatesEloContract;
use App\Models\Game;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateEloAndRank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $playerA;

    protected User $playerB;

    protected Game $game;

    protected bool $playerAWon;

    public function __construct(User $playerA, User $playerB, bool $playerAWon, ?Game $game = null)
    {
        $this->playerA = $playerA;
        $this->playerB = $playerB;
        $this->playerAWon = $playerAWon;
        $this->game = $game;
    }

    public function handle(CalculatesEloContract $eloCalculator)
    {
        $newRatings = $eloCalculator($this->playerA, $this->playerB, $this->playerAWon);

        if ($newRatings->isEmpty()) {
            return;
        }

        // TODO: Database transaction this so if one fails both should be reverted
        $this->playerA->newElo($newRatings->get('playerANewElo'), $this->game);
        $this->playerB->refresh();
        $this->playerB->newElo($newRatings->get('playerBNewElo'), $this->game);
    }
}
