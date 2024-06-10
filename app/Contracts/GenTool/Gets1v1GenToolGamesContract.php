<?php

namespace App\Contracts\GenTool;

use Illuminate\Support\Collection;

interface Gets1v1GenToolGamesContract
{
    public function __invoke(string $url): Collection;
}
