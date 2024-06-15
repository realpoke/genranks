<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;

    private static $maxValidationAttempts = 6;

    public static $allowedTableFields = [
        'status',
        'hash',
    ];

    protected $fillable = [
        'status',
        'hash',
        'summary',
        'meta',
        'players',
    ];

    public function route(): string
    {
        return route('game.show', ['game' => $this]);
    }

    protected function casts(): array
    {
        return [
            'summary' => 'array',
            'meta' => 'array',
            'players' => 'array',
            'status' => GameStatus::class,
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(GameUser::class)
            ->withPivot(GameUser::FIELDS)
            ->withTimestamps();
    }

    public function scopeUnverified(Builder $query): Builder
    {
        return $query->whereIn('status', [
            GameStatus::AWAITING->value,
            GameStatus::FAILED->value,
            GameStatus::VALIDATING->value,
            GameStatus::CALCULATING->value,
            GameStatus::INVALID->value,
        ]);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereIn('status', [
            GameStatus::VALID->value,
            GameStatus::DRAW->value,
        ]);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->whereIn('status', [
            GameStatus::FAILED->value,
            GameStatus::INVALID->value,
        ]);
    }

    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->whereLike(['hash'], $searchTerm);
    }
}
