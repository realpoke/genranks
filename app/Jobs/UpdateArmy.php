<?php

namespace App\Jobs;

use App\Contracts\UpdatesArmyMatchupContract;
use App\Enums\Army;
use App\Enums\GameType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateArmy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Army $winningArmy;

    protected Army $opponentArmy;

    protected GameType $gameType;

    /**
     * Create a new job instance.
     */
    public function __construct(Army $winningArmy, Army $opponentArmy, GameType $gameType)
    {
        $this->winningArmy = $winningArmy;
        $this->opponentArmy = $opponentArmy;
        $this->gameType = $gameType;
    }

    /**
     * Execute the job.
     */
    public function handle(UpdatesArmyMatchupContract $armyUpdater): void
    {
        $armyUpdater($this->winningArmy, $this->opponentArmy, $this->gameType);
    }
}
