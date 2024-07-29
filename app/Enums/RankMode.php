<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum RankMode: string
{
    use EnumArray;

    case ALL = 'all';
    case BALANCED = 'balanced';
    case FUN = 'fun';

    public const MAX_ELO_DIFFERENCE_FOR_BALANCED = 500;
}
