<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum Army: string
{
    use EnumArray;

    case GLA = 'GLA';
    case GLAStealth = 'GLA Stealth';
    case GLAToxic = 'GLA Toxin';
    case GLADemolition = 'GLA Demo';

    case USA = 'USA';
    case USAAirforce = 'USA Airforce';
    case USALaser = 'USA Lazr';
    case USASuperweapon = 'USA Superweapon';

    case China = 'China';
    case ChinaInfantry = 'China Infantry';
    case ChinaNuke = 'China Nuke';
    case ChinaTank = 'China Tank';
}
