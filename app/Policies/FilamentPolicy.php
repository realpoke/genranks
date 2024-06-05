<?php

namespace App\Policies;

use App\Models\User;

class FilamentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('viewAny:filament');
    }
}
