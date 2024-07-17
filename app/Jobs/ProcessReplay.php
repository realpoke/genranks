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
use Illuminate\Support\Facades\Log;
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
                $newSummary = collect($replayData->get('summary'));

                foreach ($newSummary as $newPlayer) {
                    if ($newPlayer['Name'] === $existingData['Name']) {
                        // Update the 'Win' field to true if either is true, never change true to false
                        $existingData['Win'] = $existingData['Win'] || $newPlayer['Win'];
                        break;
                    }
                }

                return $existingData;
            });

            if ($gameFound->users->count() < $gameFound->type->replaysNeededForValidation()) {
                $updated = $gameFound->update([
                    'summary' => $updatedSummary,
                ]);

                return;
            }

            $updated = $gameFound->update([
                'summary' => $updatedSummary,
                'status' => GameStatus::VALIDATING,
            ]);

            Log::debug('Sending game to vlaidation queue');
            Log::debug('Game status: '.$gameFound->status->value);

            ValidateGame::dispatch($gameFound)->onQueue('sequential');

            return;
        }

        $mapFound = Map::where('hash', $replayData->get('meta')['MapHash'])->first();

        $players = $replayData->get('players');
        $gameType = $this->determineGameType($players);

        $game = $this->user->games()->create([
            'hash' => $replayData->get('hash'),
            'summary' => $replayData->get('summary'),
            'meta' => $replayData->get('meta'),
            'players' => $players,
            'map_id' => $mapFound?->id,
            'type' => $gameType,
        ], [
            'header' => $replayData->get('header'),
            'anticheat' => $this->anticheat,
        ]);
    }

    private function determineGameType(array $players): GameType
    {
        // TODO: Remove replays with unsupported game types
        $playerCount = count($players);
        $teams = array_unique(array_column($players, 'Team'));
        $teamCount = count(array_filter($teams, fn ($team) => $team !== '-1'));

        if ($playerCount === 2) {
            return GameType::ONE_ON_ONE;
        } elseif ($teamCount === 0) {
            return match ($playerCount) {
                3 => GameType::FREE_FOR_ALL_THREE,
                4 => GameType::FREE_FOR_ALL_FOUR,
                5 => GameType::FREE_FOR_ALL_FIVE,
                6 => GameType::FREE_FOR_ALL_SIX,
                7 => GameType::FREE_FOR_ALL_SEVEN,
                8 => GameType::FREE_FOR_ALL_EIGHT,
                default => GameType::UNSUPPORTED,
            };
        } elseif ($teamCount === 2) {
            return match ($playerCount) {
                2 => GameType::ONE_ON_ONE,
                4 => GameType::TWO_ON_TWO,
                6 => GameType::THREE_ON_THREE,
                8 => GameType::FOUR_ON_FOUR,
                default => GameType::UNSUPPORTED,
            };
        }

        return GameType::UNSUPPORTED;
    }
}
