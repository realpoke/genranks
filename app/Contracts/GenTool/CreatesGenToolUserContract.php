<?php

namespace App\Contracts\GenTool;

use Illuminate\Support\Collection;

interface CreatesGenToolUserContract
{
    public function __invoke(string ...$nicknames): Collection;
}
