<?php

namespace App\Contracts\GenTool;

use Carbon\CarbonInterface;

interface GetsGenToolUsersContract
{
    public function __invoke(CarbonInterface $day): \Illuminate\Support\Collection;
}
