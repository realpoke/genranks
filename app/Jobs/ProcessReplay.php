<?php

namespace App\Jobs;

use App\Contracts\ParsesReplayContract;
use App\Enums\GameStatus;
use App\Models\Game;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessReplay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $fileName)
    {
        $this->user = $user;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(ParsesReplayContract $parser): void
    {
        $gameSetup = $this->user->setupGame($this->fileName);

        if (! $gameSetup) {
            return;
        }

        $game = Game::without('uploader')->where('file', $this->fileName)->first();

        if (is_null($game)) {
            Storage::disk('replays')->delete($this->fileName);

            return;
        }
        $game->update(['status' => GameStatus::PROCESSING]);
        $game->updateData($parser($this->fileName));

        Storage::disk('replays')->delete($this->fileName);
    }
}
