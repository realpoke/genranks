<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum GameStatus: string
{
    use EnumArray;

    case AWAITING = 'awaiting processing';
    case PROCESSING = 'processing';
    case FAILED = 'failed processing';
    case VALID = 'valid';
    case INVALID = 'invalid';

    public function classes(): string
    {
        return match ($this) {
            self::AWAITING => 'bg-sky-500',
            self::PROCESSING => 'bg-amber-300',
            self::FAILED, self::INVALID => 'bg-rose-700',
            self::VALID => 'bg-emerald-500',
        };
    }

    public function animation(): ?string
    {
        return match ($this) {
            self::AWAITING, self::PROCESSING => 'animate-ping',
            default => 'opacity-25',
        };
    }
}
