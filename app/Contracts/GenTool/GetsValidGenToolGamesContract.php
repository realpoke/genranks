<?php

namespace App\Contracts\GenTool;

use Illuminate\Support\Collection;

interface GetsValidGenToolGamesContract
{
    public function __invoke(string $url): Collection;
}
