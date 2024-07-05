<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum TournamentStatus: string
{
    use EnumArray;

    case UPCOMING = 'upcoming';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ONGOING = 'ongoing';
    case CANCELLED = 'cancelled';
    case FINISHED = 'finished';
}
