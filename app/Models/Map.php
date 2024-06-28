<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    use HasFactory;

    protected $fillable = [
        'hash',
        'name',
        'ranked',
    ];

    protected $casts = [
        'ranked' => 'boolean',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
