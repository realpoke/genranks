<?php

namespace App\Jobs;

use App\Contracts\ParsesReplayContract;
use App\Enums\GameStatus;
use App\Enums\GameType;
use App\Models\Game;
use App\Models\Map;
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

    protected $anticheat;

    /**
     * Create a new job instance.
     */
    public function __construct(
        User $user,
        string $fileName,
        bool $anticheat = false
    ) {
        $this->user = $user;
        $this->fileName = $fileName;
        $this->anticheat = $anticheat;
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

            if ($gameFound->users->contains($this->user)) {

                return;
            }

            $this->user->games()->attach($gameFound->id, [
                'header' => $replayData->get('header'),
                'anticheat' => $this->anticheat,
            ]);

            // TODO: Make sure this is actaully needed
            $gameFound->refresh(); // Make sure the data is updated with both users.

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

            $updated = $gameFound->update([
                'summary' => $updatedSummary,
                'status' => GameStatus::VALIDATING,
            ]);

            ValidateGame::dispatch($gameFound)->onQueue('sequential');

            return;
        }

        $mapFound = Map::where('hash', $replayData->get('meta')['MapHash'])->first();

        $game = $this->user->games()->create([
            'hash' => $replayData->get('hash'),
            'summary' => $replayData->get('summary'),
            'meta' => $replayData->get('meta'),
            'players' => $replayData->get('players'),
            'map_id' => $mapFound?->id,
            'type' => GameType::ONE_ON_ONE,
        ], [
            'header' => $replayData->get('header'),
            'anticheat' => $this->anticheat,
        ]);
    }
}
