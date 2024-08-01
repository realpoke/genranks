<?php

namespace App\Models;

use App\Enums\GameType;
use Illuminate\Database\Eloquent\Builder;
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
        'file',
        'type',
    ];

    protected $casts = [
        'ranked' => 'boolean',
        'type' => GameType::class,
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public static function getAllowedListFields(): array
    {
        return [
            'hash',
            'name',
            'ranked',
            'file',
            'type',
        ];
    }

    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->whereLike([
            'hash',
            'name',
            'id',
        ], $searchTerm);
    }
}
