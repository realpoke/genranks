<?php

namespace App\Enums;

use App\Traits\EnumArray;
use Illuminate\Support\Facades\Storage;

enum Side: string
{
    use EnumArray;

    case GLA = 'GLA';
    case USA = 'USA';
    case CHINA = 'China';
    case GLA_DEMO = 'GLA Demo';
    case USA_LAZR = 'USA Lazr';
    case GLA_TOXIN = 'GLA Toxin';
    case CHINA_NUKE = 'China Nuke';
    case CHINA_TANK = 'China Tank';
    case GLA_STEALTH = 'GLA Stealth';
    case USA_AIRFORCE = 'USA Airforce';
    case CHINA_INFANTRY = 'China Infantry';
    case USA_SUPERWEAPON = 'USA Superweapon';
    case RANDOM = 'Random';

    public function getBaseSide(): self
    {
        return match ($this) {
            self::GLA_DEMO,
            self::GLA_TOXIN,
            self::GLA_STEALTH,
            self::GLA => self::GLA,
            self::USA_AIRFORCE,
            self::USA_LAZR,
            self::USA_SUPERWEAPON,
            self::USA => self::USA,
            self::CHINA_INFANTRY,
            self::CHINA_NUKE,
            self::CHINA_TANK,
            self::CHINA => self::CHINA,
            default => self::RANDOM,
        };
    }

    public static function favoriteSide(array $sides): self
    {
        // Initialize counts
        $sideCounts = [
            self::GLA->value => 0,
            self::USA->value => 0,
            self::CHINA->value => 0,
        ];

        // Aggregate counts
        foreach ($sides as $key => $count) {
            $side = self::from($key)->getBaseSide();
            $sideCounts[$side->value] += $count;
        }

        // Determine the favorite side with a 10% margin
        arsort($sideCounts);
        $sidesArray = array_keys($sideCounts);

        if (count($sidesArray) > 1 && $sideCounts[$sidesArray[0]] >= 1.1 * $sideCounts[$sidesArray[1]]) {
            return self::from($sidesArray[0]);
        }

        return self::RANDOM;
    }

    public function getProfileImageUrl(RankBracket $rankBracket): string
    {
        return Storage::disk('images')->url('brackets/profile/'.strtolower($rankBracket->value.'_'.strtolower($this->getBaseSide()->value)).'.png');
    }

    public function getBadgeImageUrl(RankBracket $rankBracket): string
    {
        return Storage::disk('images')->url('brackets/badge/'.strtolower($rankBracket->value.'_'.strtolower($this->getBaseSide()->value)).'.png');
    }

    public static function isValidSide(string $side): bool
    {
        return in_array($side, self::values(), true);
    }
}
