<?php

namespace App\Jobs;

use App\Contracts\CalculatesEloContract;
use App\Enums\EloRankType;
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
        $newRatingsAll = $eloCalculator($this->playerA, $this->playerB, $this->playerAWon);

        if ($newRatingsAll->isEmpty()) {
            return;
        }

        // TODO: Database transaction this so if one fails both should be reverted
        $this->playerA->newElo($newRatingsAll->get('playerANewElo'), $this->game);
        $this->playerA->changeElo($newRatingsAll->get('playerAChangedElo'), rankType: EloRankType::WEEKLY);
        $this->playerA->changeElo($newRatingsAll->get('playerAChangedElo'), rankType: EloRankType::MONTHLY);
        $this->playerB->refresh();
        $this->playerB->newElo($newRatingsAll->get('playerBNewElo'), $this->game);
        $this->playerB->changeElo($newRatingsAll->get('playerBChangedElo'), rankType: EloRankType::WEEKLY);
        $this->playerB->changeElo($newRatingsAll->get('playerBChangedElo'), rankType: EloRankType::MONTHLY);
    }
}
