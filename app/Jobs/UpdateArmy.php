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
use Illuminate\Support\Facades\Log;

class UpdateArmy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $winningArmies;

    protected array $losingArmies;

    protected GameType $gameType;

    /**
     * Create a new job instance.
     */
    public function __construct(array|Army $winningArmies, array|Army $losingArmies, GameType $gameType)
    {
        if (! is_array($winningArmies)) {
            $winningArmies = [$winningArmies];
        }

        if (! is_array($losingArmies)) {
            $losingArmies = [$losingArmies];
        }

        $this->winningArmies = $winningArmies;
        $this->losingArmies = $losingArmies;
        $this->gameType = $gameType;
    }

    /**
     * Execute the job.
     */
    public function handle(UpdatesArmyMatchupContract $armyUpdater): void
    {
        if (empty($this->winningArmies) || empty($this->losingArmies)) {
            Log::error('Both winning and losing armies must be provided');

            return;
        }

        foreach ($this->winningArmies as $winningArmy) {
            if (! ($winningArmy instanceof Army)) {
                Log::error('Invalid winning army: '.$winningArmy);

                return;
            }
        }

        foreach ($this->losingArmies as $losingArmy) {
            if (! ($losingArmy instanceof Army)) {
                Log::error('Invalid losing army: '.$losingArmy);

                return;
            }
        }
        $armyUpdater($this->winningArmies, $this->losingArmies, $this->gameType);
    }
}
