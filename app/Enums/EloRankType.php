<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum EloRankType: string
{
    use EnumArray;

    case ALL = 'all';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';

    public function databaseEloField(GameType $gameType): string
    {
        return match (true) {
            $gameType->isFreeForAll() => match ($this) {
                self::ALL => 'ffa_elo',
                self::WEEKLY => 'ffa_weekly_elo',
                self::MONTHLY => 'ffa_monthly_elo',
            },
            default => match ($this) {
                self::ALL => 'elo',
                self::WEEKLY => 'weekly_elo',
                self::MONTHLY => 'monthly_elo',
            },
        };
    }

    public function databaseRankField(GameType $gameType): string
    {
        return match (true) {
            $gameType->isFreeForAll() => match ($this) {
                self::ALL => 'ffa_rank',
                self::WEEKLY => 'ffa_weekly_rank',
                self::MONTHLY => 'ffa_monthly_rank',
            },
            default => match ($this) {
                self::ALL => 'rank',
                self::WEEKLY => 'weekly_rank',
                self::MONTHLY => 'monthly_rank',
            },
        };
    }
}
