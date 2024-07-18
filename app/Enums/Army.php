<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum Army: string
{
    use EnumArray;

    case GLA = 'gla';
    case GLAStealth = 'gla-stealth';
    case GLAToxic = 'gla-toxic';
    case GLADemolition = 'gla-demolition';

    case USA = 'usa';
    case USAAirforce = 'usa-airforce';
    case USALaser = 'usa-laser';
    case USASuperweapon = 'usa-superweapon';

    case China = 'china';
    case ChinaInfantry = 'china-infantry';
    case ChinaNuke = 'china-nuke';
    case ChinaTank = 'china-tank';
}
