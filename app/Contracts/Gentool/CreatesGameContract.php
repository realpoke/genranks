<?php

namespace App\Contracts\Gentool;

use Illuminate\Support\Collection;

interface CreatesGameContract
{
    public function create(Collection $users);
}
