<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RankBracket;
use App\Enums\Side;
use App\Notifications\WelcomeNotification;
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
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasClan, HasElo, HasFactory, HasRoles, Notifiable;

    public const DEFAULT_ROLE = 'user';

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
        'ffa_weekly_elo',
        'ffa_weekly_rank',
        'ffa_monthly_elo',
        'ffa_monthly_rank',
        'ffa_elo',
        'ffa_rank',
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
            $user->assignRole(self::DEFAULT_ROLE);

            $user->notify((new WelcomeNotification())->delay(now()->addMinutes(15)));
        });
    }

    public function route(): string
    {
        return route('profile.show', ['user' => $this]);
    }

    public function badgeUrl(string $rankField = 'rank', string $eloField = 'elo'): string
    {
        if ($this->games->isEmpty()) {
            return Storage::disk('images')->url('brackets/badge/unranked.png');
        }

        if ($this->{$rankField} == null) {
            return Storage::disk('images')->url('brackets/badge/unranked.png');
        }

        return $this->favoriteSide()->getBadgeImageUrl($this->bracket($eloField));
    }

    public function pictureUrl(string $rankField = 'rank', string $eloField = 'elo'): string
    {
        if ($this->games->isEmpty()) {
            return Storage::disk('images')->url('brackets/profile/unranked.png');
        }

        if ($this->{$rankField} == null) {
            return Storage::disk('images')->url('brackets/profile/unranked.png');
        }

        return $this->favoriteSide()->getProfileImageUrl($this->bracket($eloField));
    }

    public function favoriteSide(): Side
    {
        if ($this->stats['Sides'] == null) {

            return Side::RANDOM;
        }

        return Side::favoriteSide($this->stats['Sides']);
    }

    public function bracket(string $eloField = 'elo'): RankBracket
    {
        return RankBracket::getRankBracketByElo($this->{$eloField});
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('viewAny:filament');
    }

    public function scopeRanked(Builder $query, string $rankField = 'rank'): Builder
    {
        return $query->whereNotNull($rankField);
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
