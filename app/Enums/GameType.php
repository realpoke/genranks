<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum GameType: string
{
    use EnumArray;

    case ONE_ON_ONE = 'one-on-one';
    case TWO_ON_TWO = 'two-on-two';

    case FREE_FOR_ALL = 'free-for-all';

    case UNSUPPORTED = 'unsupported';
}
