<?php

namespace App\Livewire\Lists;

use App\Livewire\DynamicList;
use App\Models\Game as ModelsGame;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

class Game extends DynamicList
{
    protected $rowView = 'items.games';

    #[Computed()]
    protected function getModel(): Model
    {
        return new (ModelsGame::class);
    }

    protected function setupExtraFilters(): void
    {
        //
    }
}
