<?php

namespace App\Livewire;

use App\Models\Game as ModelsGame;
use Livewire\Component;
use Livewire\WithPagination;

class Game extends Component
{
    use WithPagination;

    public function showGame(ModelsGame $game)
    {
        return $this->redirect($game->route(), navigate: true);
    }

    public function render()
    {
        return view('livewire.game', [
            'games' => ModelsGame::latest()->paginate(12),
        ]);
    }
}
