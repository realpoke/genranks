<?php

namespace App\Models;

use App\Enums\GameType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

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

    public function downloadURL(): ?string
    {
        if (! $this->file) {
            return null;
        }

        return URL::temporarySignedRoute(
            'map.download',
            Carbon::now()->addMinutes(2),
            ['map' => $this->id]
        );
    }

    public function path(): ?string
    {
        if (! $this->file) {
            return null;
        }

        return implode('/', ['maps', $this->file, $this->name]);
    }
}
