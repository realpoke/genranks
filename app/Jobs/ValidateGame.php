<?php

namespace App\Jobs;

use App\Contracts\ValidatesGameContract;
use App\Models\Game;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ValidateGame implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $game;

    /**
     * Create a new job instance.
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function middleware()
    {
        return [new WithoutOverlapping];
    }

    /**
     * Execute the job.
     */
    public function handle(ValidatesGameContract $validator): void
    {
        $validator($this->game);
    }
}
