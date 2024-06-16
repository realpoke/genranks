<?php

namespace App\Jobs;

use App\Contracts\ValidatesGameContract;
use App\Models\Game;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

    /**
     * Execute the job.
     */
    public function handle(ValidatesGameContract $validator): void
    {
        Log::debug('Starting game validation: '.$this->game->hash);
        $validated = $validator($this->game);
    }
}
