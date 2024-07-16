<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum GameType: string
{
    use EnumArray;

    case ONE_ON_ONE = 'one-on-one';
    case TWO_ON_TWO = 'two-on-two';
    case THREE_ON_THREE = 'three-on-three';
    case FOUR_ON_FOUR = 'four-on-four';

    case FREE_FOR_ALL = 'free-for-all';

    case UNSUPPORTED = 'unsupported';

    public function replaysNeededForValidation(): int
    {
        return match ($this) {
            self::ONE_ON_ONE => 2,
            self::TWO_ON_TWO => 4,
            self::THREE_ON_THREE => 6,
            self::FOUR_ON_FOUR => 8,
            self::FREE_FOR_ALL => 4,
            default => 0,
        };
    }

    public function validPlayerCount($playerCount): bool
    {
        return match ($this) {
            self::ONE_ON_ONE => $playerCount == 2,
            self::TWO_ON_TWO => $playerCount == 4,
            self::THREE_ON_THREE => $playerCount == 6,
            self::FOUR_ON_FOUR => $playerCount == 8,
            self::FREE_FOR_ALL => $playerCount == 4 || $playerCount == 6 || $playerCount == 8,
            default => false,
        };
    }

    public static function validGenToolGameType(): array
    {
        return [
            '1v1',
            '2v2',
            '3v3',
            '4v4',
            '1v1v1',
            '1v1v1v1',
            '1v1v1v1v1',
            '1v1v1v1v1v1',
            '1v1v1v1v1v1v1',
            '1v1v1v1v1v1v1v1',
        ];
    }
}
