<?php

namespace App\Enums;

enum RankBracket: string
{
    case PRIVATE = 'private';
    case CORPORAL = 'corporal';
    case SERGEANT = 'sergeant';
    case LIEUTENANT = 'lieutenant';
    case CAPTAIN = 'captain';
    case MAJOR = 'major';
    case COLONEL = 'colonel';
    case BRIGADIER_GENERAL = 'brigadiergeneral';
    case GENERAL = 'general';
    case COMMANDER_IN_CHIEF = 'commanderinchief';

    public static function getRankBracketByElo(int $elo): self
    {
        return match (true) {
            $elo > 2800 => RankBracket::COMMANDER_IN_CHIEF,
            $elo > 2600 => RankBracket::BRIGADIER_GENERAL,
            $elo > 2400 => RankBracket::GENERAL,
            $elo > 2200 => RankBracket::COLONEL,
            $elo > 2000 => RankBracket::MAJOR,
            $elo > 1800 => RankBracket::CAPTAIN,
            $elo > 1600 => RankBracket::LIEUTENANT,
            $elo > 1400 => RankBracket::SERGEANT,
            $elo > 1200 => RankBracket::CORPORAL,
            default => RankBracket::PRIVATE,
        };
    }
}
