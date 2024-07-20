<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matchup extends Model
{
    use HasFactory;

    protected $fillable = [
        'armies',
        'opponents',
        'score',
        'game_type',
    ];

    protected function casts(): array
    {
        return [
            'armies' => 'array',
            'opponents' => 'array',
        ];
    }
}
