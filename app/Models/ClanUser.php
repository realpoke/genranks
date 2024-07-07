<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClanUser extends Pivot
{
    protected $table = 'clan_user';

    protected $fillable = [
        'clan_id',
        'user_id',
        'status',
    ];

    public const FIELDS = ['status'];

    public function clan(): BelongsTo
    {
        return $this->belongsTo(Clan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
