<?php

namespace App\Contracts\Gentool;

use Carbon\CarbonInterface;

interface GetsUsersContract
{
    public function users(CarbonInterface $day): \Illuminate\Support\Collection;
}
