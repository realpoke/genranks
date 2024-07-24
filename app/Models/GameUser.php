<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GameUser extends Pivot
{
    protected $table = 'game_user';

    protected $fillable = [
        'game_id',
        'user_id',
        'elo_change',
        'header',
        'summary',
        'anticheat',
        'ffa_elimination_order',
        'commands',
    ];

    protected function casts(): array
    {
        return [
            'header' => 'array',
            'summary' => 'array',
            'commands' => 'array',
        ];
    }

    public const FIELDS = [
        'elo_change',
        'header',
        'summary',
        'anticheat',
        'ffa_elimination_order',
        'commands',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
