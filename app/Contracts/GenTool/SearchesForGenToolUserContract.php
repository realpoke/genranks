<?php

namespace App\Contracts\GenTool;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

interface SearchesForGenToolUserContract
{
    public function __invoke(CarbonInterface $day, ...$nicknames): Collection;
}
