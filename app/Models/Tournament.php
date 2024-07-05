<?php

namespace App\Models;

use App\Enums\TournamentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_at',
        'stages',
        'minimum_elo',
        'invite_only',
        'status',
        'host_id',
    ];

    protected function casts(): array
    {
        return [
            'stages' => 'array',
            'status' => TournamentStatus::class,
            'start_at' => 'datetime',
        ];
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)
            ->using(GameTournament::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, GameUser::class);
    }
}
