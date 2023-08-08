<?php

namespace App\Contracts\Gentool;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

interface CreatesGameContract
{
    public function create(
        Collection $users,
        ?ProgressBar $progress
    );
}
