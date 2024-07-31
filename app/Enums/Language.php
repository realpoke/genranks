<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum Language: string
{
    use EnumArray;

    case ENGLISH = 'en';
    case DANISH = 'da';
}
