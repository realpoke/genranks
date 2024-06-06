<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;

class ShowGame extends Component
{
    public Game $game;

    public function mount(Game $game)
    {
        $this->game = $game;
    }

    public function render()
    {
        return view('livewire.show-game');
    }
}
