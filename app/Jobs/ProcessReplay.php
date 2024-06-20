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
use Illuminate\Support\Facades\Log;
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
        Log::debug('Parsing replay: '.$this->fileName);
        $replayData = $parser($this->fileName);
        Storage::disk('replays')->delete($this->fileName);

        if ($replayData->isEmpty()) {
            return;
        }

        $gameFound = Game::where('hash', $replayData->get('hash'))->first();

        if (! is_null($gameFound)) {
            Log::debug('Found game');

            if ($gameFound->users->contains($this->user)) {
                Log::debug('Exists for same user');

                return;
            }

            Log::debug('Attaching');
            $this->user->games()->attach($gameFound->id, [
                'header' => $replayData->get('header'),
            ]);

            // TODO: Make sure this is actaully needed
            $gameFound->refresh(); // Make sure the data is updated with both users.

            Log::debug('Updating summary');
            Log::debug($gameFound->summary);
            Log::debug($replayData->get('summary'));
            // If a winner is in the new replay replace it.
            $updatedSummary = collect($gameFound->summary)->map(function ($existingData) use ($replayData) {
                // Find the new player data in the replay data
                $newPlayer = collect($replayData)->firstWhere('Name', $existingData['Name']);

                // If the new player data exists, update the existing data
                if ($newPlayer) {
                    // Update the 'Win' field to true if either is true
                    $existingData['Win'] = $existingData['Win'] || $newPlayer['Win'];
                }

                return $existingData;
            });
            Log::debug('New summary: '.$updatedSummary);

            Log::debug('Updating game');
            $updated = $gameFound->update([
                'summary' => $updatedSummary,
                'status' => GameStatus::VALIDATING,
            ]);

            Log::debug('Sending to validation');
            ValidateGame::dispatch($gameFound)->onQueue('sequential');

            return;
        }

        Log::debug('Creating new game');
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
