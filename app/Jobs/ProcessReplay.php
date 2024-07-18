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
            Log::info('Skipping replay processing: empty data or observer upload');

            return;
        }

        $gameFound = Game::where('hash', $replayData->get('hash'))->first();

        if (! is_null($gameFound)) {
            if ($gameFound->users->contains($this->user)) {
                return;
            }

            $playerSummary = $this->getPlayerSummary($replayData->get('summary'), $replayData->get('header'));

            $this->user->games()->attach($gameFound->id, [
                'header' => $replayData->get('header'),
                'anticheat' => $this->anticheat,
                'summary' => $playerSummary,
            ]);

            $gameFound->refresh();

            if ($gameFound->users->count() < $gameFound->type->replaysNeededForValidation()) {
                return;
            }

            $gameFound->update([
                'status' => GameStatus::VALIDATING,
            ]);

            Log::debug('Sending game to validation queue');
            Log::debug('Game status: '.$gameFound->status->value);

            ValidateGame::dispatch($gameFound)->onQueue('sequential');

            return;
        }

        $mapFound = Map::where('hash', $replayData->get('meta')['MapHash'])->first();

        $players = $replayData->get('players');
        $gameType = $this->determineGameType($players);

        $header = $replayData->get('header');
        $playerSummary = $this->getPlayerSummary($replayData->get('summary'), $header);

        if ($gameType === GameType::UNSUPPORTED) {
            Log::warning('Unsupported game type detected', [
                'hash' => $replayData->get('hash'),
                'players' => $players,
            ]);

            return;
        }

        $game = $this->user->games()->create([
            'hash' => $replayData->get('hash'),
            'meta' => $replayData->get('meta'),
            'players' => $players,
            'map_id' => $mapFound?->id,
            'type' => $gameType,
        ], [
            'header' => $header,
            'anticheat' => $this->anticheat,
            'summary' => $playerSummary,
        ]);
    }

    private function getPlayerSummary(array $gameSummary, array $header): ?array
    {
        $ownerSlot = $header['ArrayReplayOwnerSlot'] ?? null;

        if ($ownerSlot === null || ! isset($gameSummary[$ownerSlot])) {
            return null;
        }

        return $gameSummary[$ownerSlot];
    }

    private function determineGameType(array $players): GameType
    {
        $playerCount = count($players);
        $teams = array_column($players, 'Team');
        $uniqueTeams = array_unique($teams);

        if ($playerCount === 2) {
            return GameType::ONE_ON_ONE;
        }

        // Check for FFA: either all unique teams, all on team "-1", or all but one player on team "-1"
        $nonDefaultTeamCount = count(array_filter($teams, fn ($team) => $team !== '-1'));
        if (count($uniqueTeams) === $playerCount || $nonDefaultTeamCount === 0 || ($nonDefaultTeamCount <= 1 && in_array('-1', $teams))) {
            return match ($playerCount) {
                3 => GameType::FREE_FOR_ALL_THREE,
                4 => GameType::FREE_FOR_ALL_FOUR,
                5 => GameType::FREE_FOR_ALL_FIVE,
                6 => GameType::FREE_FOR_ALL_SIX,
                7 => GameType::FREE_FOR_ALL_SEVEN,
                8 => GameType::FREE_FOR_ALL_EIGHT,
                default => GameType::UNSUPPORTED,
            };
        }

        // Check for team games
        if (count($uniqueTeams) === 2) {
            $teamCounts = array_count_values($teams);
            if (count($teamCounts) === 2 && $teamCounts[array_key_first($teamCounts)] === $teamCounts[array_key_last($teamCounts)]) {
                return match ($playerCount) {
                    4 => GameType::TWO_ON_TWO,
                    6 => GameType::THREE_ON_THREE,
                    8 => GameType::FOUR_ON_FOUR,
                    default => GameType::UNSUPPORTED,
                };
            }
        }

        return GameType::UNSUPPORTED;
    }
}
