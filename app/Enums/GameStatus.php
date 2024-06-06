<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum GameStatus: string
{
    use EnumArray;

    case AWAITING = 'awaiting processing';
    case PROCESSING = 'processing';
    case FAILED = 'failed processing';
    case VALID = 'valid';
    case INVALID = 'invalid';
}
