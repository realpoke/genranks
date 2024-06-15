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
        $replayData = $parser($this->fileName);
        Storage::disk('replays')->delete($this->fileName);

        if ($replayData->isEmpty()) {
            return;
        }

        $gameFound = Game::where('hash', $replayData->get('hash'))->first();

        if (! is_null($gameFound)) {
            dump('Found game');

            if ($gameFound->users->contains($this->user)) {
                dump('Exists for same user');

                return;
            }

            dump('Attaching');
            $this->user->games()->attach($gameFound->id, [
                'header' => $replayData->get('header'),
            ]);

            dump('Updating summary');
            // If a winner is in the new replay replace it.
            $updatedSummary = collect($gameFound->summary)->map(function ($existingData) use ($replayData) {
                $newPlayer = $replayData->firstWhere('Name', $existingData['Name']);

                if ($newPlayer && $existingData['Win'] === false && $newPlayer['Win'] === true) {
                    $existingData['Win'] = true;
                }

                return $existingData;
            });

            dump('Updating game');
            $updated = $gameFound->update([
                'summary' => $updatedSummary,
                'status' => GameStatus::VALIDATING,
            ]);

            dump('Sending to validation');
            ValidateGame::dispatch($gameFound)->onQueue('sequential');

            return;
        }

        dump('Creating new game');
        $game = $this->user->games()->create([
            'hash' => $replayData->get('hash'),
            'summary' => $replayData->get('summary'),
            'meta' => $replayData->get('meta'),
            'players' => $replayData->get('players'),
        ], [
            'header' => $replayData->get('header'),
        ]);
    }
}
