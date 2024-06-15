<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum GameStatus: string
{
    use EnumArray;

    case AWAITING = 'awaiting processing';
    case FAILED = 'failed processing';
    case VALIDATING = 'validating';
    case VALID = 'valid';
    case DRAW = 'draw';
    case CALCULATING = 'calculating results';
    case INVALID = 'invalid';

    public function classes(): string
    {
        return match ($this) {
            self::AWAITING, self::CALCULATING => 'bg-sky-500',
            self::VALIDATING, self::DRAW => 'bg-amber-300',
            self::FAILED, self::INVALID => 'bg-rose-700',
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
