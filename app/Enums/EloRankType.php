<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum EloRankType: string
{
    use EnumArray;

    case ALL = 'all';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public function databaseEloField(): string
    {
        return match ($this) {
            self::ALL => 'elo',
            self::WEEKLY => 'weekly_elo',
            self::MONTHLY => 'monthly_elo',
        };
    }

    public function databaseRankField(): string
    {
        return match ($this) {
            self::ALL => 'rank',
            self::WEEKLY => 'weekly_rank',
            self::MONTHLY => 'monthly_rank',
        };
    }
}
