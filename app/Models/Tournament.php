<?php

namespace App\Models;

use App\Enums\TournamentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
