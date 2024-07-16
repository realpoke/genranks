<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\EloRankType;
use App\Traits\HasClan;
use App\Traits\HasElo;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasClan, HasElo, HasFactory, HasRoles, Notifiable;

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
        'weekly_elo',
        'weekly_rank',
        'monthly_elo',
        'monthly_rank',
        'elo',
        'rank',
        'fake',
        'stats',
        'gentool_ids',
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
            'stats' => 'array',
            'gentool_ids' => 'array',
            'fake' => 'boolean',
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

    public function scopeRanked(Builder $query, EloRankType $rankType = EloRankType::ALL): Builder
    {
        return $query->whereNotNull($rankType->databaseRankField());
    }

    public function scopeFake(Builder $query): Builder
    {
        return $query->where('fake', true);
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)
            ->using(GameUser::class)
            ->withPivot(GameUser::FIELDS)
            ->withTimestamps();
    }

    public function tournaments(): HasManyThrough
    {
        return $this->hasManyThrough(Tournament::class, Game::class);
    }

    public function tournamentsAsHost(): HasMany
    {
        return $this->hasMany(Tournament::class, 'host_id');
    }
}
