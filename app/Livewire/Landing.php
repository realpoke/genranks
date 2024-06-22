<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Landing extends Component
{
    public $topCommanders;

    public $eloChanged;

    public $gamesProcessed;

    public $activeUsers;

    public function mount()
    {
        $this->eloChanged = Cache::remember('elo-changed', 60, function () {
            return GameUser::where('updated_at', '>', now()->subDay())->sum('elo_change');
        });

        $this->gamesProcessed = Cache::remember('games-processed', 60, function () {
            return Game::verified()->count();
        });

        $this->activeUsers = Cache::remember('active-users', 60, function () {
            return User::where('updated_at', '>', now()->subDays(7))->count();
        });

        $this->topCommanders = User::ranked()->orderBy('elo', 'desc')->take(3)->get();
    }

    public function render()
    {
        return view('livewire.landing', ['topCommanders' => $this->topCommanders]);
    }
}
