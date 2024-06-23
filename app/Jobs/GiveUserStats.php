<?php

namespace App\Jobs;

use App\Contracts\GivesUserStatsContract;
use App\Models\Game;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GiveUserStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Game $game;

    /**
     * Create a new job instance.
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Execute the job.
     */
    public function handle(GivesUserStatsContract $statGiver): void
    {
        $statGiver($this->game);
    }
}
