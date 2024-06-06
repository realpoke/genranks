<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Game extends Model
{
    use HasFactory;

    protected $with = ['uploader'];

    protected $fillable = [
        'hash',
        'data',
        'uploader_id',
        'status',
        'file',
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

        // TODO: Check if game is valid or invalid; 1v1, length, etc
        return $this->update([
            'status' => GameStatus::VALID,
            'data' => $data->toArray(),
        ]);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
