<?php

namespace App\Livewire;

use App\Models\Game as ModelsGame;
use Livewire\Component;
use Livewire\WithPagination;

class Game extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.game', [
            'games' => ModelsGame::showDefault()->latest()->paginate(12),
        ]);
    }
}
