<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'hash',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(GameUser::class)
            ->withPivot(GameUser::FIELDS)
            ->withTimestamps();
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereJsonLength('data->Header->Metadata->Players', '=', DB::raw('verifications'));
    }

    public function scopeNotVerified(Builder $query): Builder
    {
        return $query->whereJsonLength('data->Header->Metadata->Players', '!=', DB::raw('verifications'));
    }
}
