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

    case FREE_FOR_ALL_THREE = 'free-for-all-3';
    case FREE_FOR_ALL_FOUR = 'free-for-all-4';
    case FREE_FOR_ALL_FIVE = 'free-for-all-5';
    case FREE_FOR_ALL_SIX = 'free-for-all-6';
    case FREE_FOR_ALL_SEVEN = 'free-for-all-7';
    case FREE_FOR_ALL_EIGHT = 'free-for-all-8';

    case UNSUPPORTED = 'unsupported';

    public function isFreeForAll(): bool
    {
        return in_array($this, [
            self::FREE_FOR_ALL_THREE,
            self::FREE_FOR_ALL_FOUR,
            self::FREE_FOR_ALL_FIVE,
            self::FREE_FOR_ALL_SIX,
            self::FREE_FOR_ALL_SEVEN,
            self::FREE_FOR_ALL_EIGHT,
        ]);
    }

    public function replaysNeededForValidation(): int
    {
        return match ($this) {
            self::ONE_ON_ONE => 2,
            self::TWO_ON_TWO => 4,
            self::THREE_ON_THREE => 6,
            self::FOUR_ON_FOUR => 8,
            self::FREE_FOR_ALL_THREE => 3,
            self::FREE_FOR_ALL_FOUR => 4,
            self::FREE_FOR_ALL_FIVE => 5,
            self::FREE_FOR_ALL_SIX => 6,
            self::FREE_FOR_ALL_SEVEN => 7,
            self::FREE_FOR_ALL_EIGHT => 8,
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
            self::FREE_FOR_ALL_THREE => $playerCount == 3,
            self::FREE_FOR_ALL_FOUR => $playerCount == 4,
            self::FREE_FOR_ALL_FIVE => $playerCount == 5,
            self::FREE_FOR_ALL_SIX => $playerCount == 6,
            self::FREE_FOR_ALL_SEVEN => $playerCount == 7,
            self::FREE_FOR_ALL_EIGHT => $playerCount == 8,
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
