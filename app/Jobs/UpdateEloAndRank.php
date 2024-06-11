<?php

namespace App\Jobs;

use App\Contracts\CalculatesEloContract;
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

    protected bool $playerAWon;

    public function __construct(User $playerA, User $playerB, bool $playerAWon)
    {
        $this->playerA = $playerA;
        $this->playerB = $playerB;
        $this->playerAWon = $playerAWon;
    }

    public function handle(CalculatesEloContract $eloCalculator)
    {
        $newRatings = $eloCalculator($this->playerA, $this->playerB, $this->playerAWon);

        if ($newRatings->isEmpty()) {
            return;
        }

        // TODO: Database transaction this so if one fails both should be reverted
        $this->playerA->newElo($newRatings->get('playerANewElo'));
        $this->playerB->newElo($newRatings->get('playerBNewElo'));
    }
}
