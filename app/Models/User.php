<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasElo;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasElo, HasFactory, HasRoles, Notifiable;

    private static $defaultRole = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'elo',
        'rank',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->assignRole($user->defaultRole);
        });
    }

    public function route(): string
    {
        return route('profile.show', ['user' => $this]);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('viewAny:filament');
    }

    public function scopeRanked($query)
    {
        return $query->whereNotNull('rank');
    }

    public function setupGame(string $fileName): bool
    {
        $newGame = $this->games()->create(['file' => $fileName]);

        return ! is_null($newGame);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class, 'uploader_id');
    }
}
