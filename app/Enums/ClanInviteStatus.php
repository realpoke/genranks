<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum ClanInviteStatus: string
{
    use EnumArray;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case BLOCKED = 'blocked';
    case LEFT = 'left';
}
