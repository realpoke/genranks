<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GameTournament extends Pivot
{
    protected $table = 'game_tournament';

    protected $fillable = [
        'game_id',
        'tournament_id',
    ];

    //public const FIELDS = [];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
