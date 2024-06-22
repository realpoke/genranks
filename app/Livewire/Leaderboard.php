<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Leaderboard extends Component
{
    use WithPagination;

    public function showUser(User $user)
    {
        return $this->redirect($user->route(), navigate: true);
    }

    public function render()
    {
        return view('livewire.leaderboard', [
            'users' => User::orderBy('rank')->ranked()->paginate(12),
        ]);
    }
}
