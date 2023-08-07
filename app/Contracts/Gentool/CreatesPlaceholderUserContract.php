<?php

namespace App\Contracts\Gentool;

use App\Models\User;

interface CreatesPlaceholderUserContract
{
    public function create(string $nickname): User;
}
