<?php

namespace App\Livewire\Lists;

use App\Livewire\DynamicList;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

class Leaderboard extends DynamicList
{
    protected $rowView = 'items.users';

    #[Computed()]
    protected function getModel(): Model
    {
        return new (User::class);
    }

    protected function setupExtraFilters(): void
    {
        //
    }
}
