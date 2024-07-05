<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum GameStatus: string
{
    use EnumArray;

    case AWAITING = 'awaiting processing';
    case VALIDATING = 'validating';
    case VALID = 'valid';
    case UNRANKED = 'unranked';
    case DRAW = 'draw'; // TODO: Remove or fix the draw status
    case CALCULATING = 'calculating results';
    case INVALID = 'invalid';

    public function done(): bool
    {
        return match ($this) {
            self::VALID, self::UNRANKED, self::DRAW => true,
            default => false,
        };
    }

    public function classes(): string
    {
        return match ($this) {
            self::AWAITING, self::CALCULATING => 'bg-sky-500',
            self::VALIDATING, self::DRAW, self::UNRANKED => 'bg-amber-300',
            self::INVALID => 'bg-rose-700',
            self::VALID => 'bg-emerald-500',
        };
    }

    public function animation(): ?string
    {
        return match ($this) {
            self::AWAITING, self::VALIDATING, self::CALCULATING => 'animate-ping',
            default => 'opacity-25',
        };
    }
}
