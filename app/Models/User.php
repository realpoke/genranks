<?php

namespace App\Models;

use App\Traits\HasElo;
use App\Traits\HasRank;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasElo, HasRank;

    public static $allowedTableFields = [
        'name',
        'nickname',
        'monthly_elo',
        'elo',
        'monthly_rank',
        'rank',
    ];

    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
        'monthly_elo',
        'elo',
        'monthly_rank',
        'rank',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'claimed_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('viewAny:filament');
    }

    public function route()
    {
        return route('profile.show', ['user' => $this]);
    }

    public function markEmailAsUnverified(): bool
    {
        $this->email_verified_at = null;

        return $this->save();
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)
            ->using(GameUser::class)
            ->withPivot(GameUser::FIELDS)
            ->withTimestamps();
    }

    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->whereLike(['name', 'nickname'], $searchTerm);
    }
}
