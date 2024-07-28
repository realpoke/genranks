<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum RankMode: string
{
    use EnumArray;

    case ALL = 'all';
    case BALANCED = 'balanced';
    case FUN = 'fun';
}
