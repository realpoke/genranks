<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Game extends Model
{
    use HasFactory;

    protected $with = ['uploader'];

    private static $maxValidationAttempts = 6;

    protected $fillable = [
        'hash',
        'data',
        'uploader_id',
        'status',
        'file',
        'validation_attempts',
    ];

    public function route(): string
    {
        return route('game.show', ['game' => $this]);
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => GameStatus::class,
        ];
    }

    public function updateData(Collection $data): bool
    {
        if ($data->isEmpty()) {
            $this->update(['status' => GameStatus::FAILED]);

            return false;
        }

        // Check if game is valid or invalid; 1v1, length, etc
        return $this->update([
            'status' => GameStatus::VALIDATING,
            'data' => $data->toArray(),
        ]);
    }

    public function validate(): bool
    {
        if ($this->status == GameStatus::VALID || $this->status == GameStatus::INVALID) {
            Log::debug('Game already valid or invalid');

            return true; // Game already valid
        }

        if ($this->status != GameStatus::VALIDATING || $this->validation_attempts > self::$maxValidationAttempts) {
            Log::debug('Too many validation attempts');

            return $this->update(['status' => GameStatus::INVALID]); // Too many validation attempts
        }
        $checkStatus = $this->validCheck();

        $this->increment('validation_attempts');

        return $this->update(['status' => $checkStatus]); // Set valid to valid check
    }

    private function validCheck(): GameStatus
    {
        if (is_null($this->hash)) {
            $newHash = $this->generateGameHash();
            if (is_null($newHash)) {
                Log::debug('Cant generate hash. Game not valid');

                return GameStatus::INVALID; // Can't generate hash. Game not valid
            }
            $this->update(['hash' => $newHash]);
        }

        // Make sure after validation same hash and Header->ReplayOwnerSlot can't be uploaded again
        $sameHashGames = Game::where('hash', $this->hash)->get();
        foreach ($sameHashGames as $game) {
            if ($game->data['Header']['ReplayOwnerSlot'] && ($game->status == GameStatus::VALID || ($game->status == GameStatus::INVALID && $game->validation_attempts > self::$maxValidationAttempts))) {
                Log::debug('Same hash and Header->ReplayOwnerSlot is valid or invalid because of too many attempts. Invalid this game');

                return GameStatus::INVALID; // Same hash and Header->ReplayOwnerSlot is valid or invalid. Invalid this game
            }
        }

        // Check there are two players and they don't have a team
        $metaPlayers = $this->data['Header']['Metadata']['Players'];
        if (count($metaPlayers) != 2) { // Two players
            Log::debug('Not exactly two players. Game not valid');

            return GameStatus::INVALID; // Not exactly two players. Game not valid
        }
        foreach ($metaPlayers as $player) {
            if ($player['Type'] != 'H' || $player['Team'] != '-1') { // Humans and no team
                Log::debug('None-human players. Game not valid');

                return GameStatus::INVALID; // None-human players. Game not valid
            }
        }

        // Check both player replays are uploaded
        $otherGame = Game::where('hash', $this->hash)->where('status', GameStatus::VALIDATING)->whereNot('id', $this->id)->first();
        if (is_null($otherGame)) {
            Log::debug('Could not find other players upload. Keep looking');

            return GameStatus::VALIDATING; // Could not find other players upload. Keep looking
        }

        if ($otherGame->uploader->id == $this->uploader->id) {
            Log::debug('Other game has same uploader and hash. Invalid other game');
            $otherGame->update(['status' => GameStatus::INVALID]); // Other game has same uploader and hash. Invalid other game

            return GameStatus::VALIDATING; // Keep looking for other users upload
        }

        if ($otherGame->data['Header']['ReplayOwnerSlot'] == $this->data['Header']['ReplayOwnerSlot']) {
            Log::debug('Other game has same Header->ReplayOwnerSlot and hash. Invalid other game');
            $otherGame->update(['status' => GameStatus::INVALID]); // Other game has same Header->ReplayOwnerSlot and hash. Invalid other game

            return GameStatus::VALIDATING; // Keep looking for ohter users upload
        }

        // TODO: Find winner in other function when game is valid

        Log::debug('Validate other game too');
        $otherGame->update(['status' => GameStatus::VALID]); // Validate other game too

        return GameStatus::VALID;
    }

    private function generateGameHash(): ?string
    {
        $d = $this->data;
        try {
            $hash = md5(
                $d['Body'][32]['TimeCode'].
                $d['Body'][50]['TimeCode'].
                $d['Header']['Hash'].
                $d['Header']['Metadata']['MapSize'].
                $d['Header']['Metadata']['Seed']
            );
        } catch (\Throwable $th) {
            return null;
        }

        return $hash;
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
