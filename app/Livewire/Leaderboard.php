<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Leaderboard extends Component
{
    public function showUser(User $user)
    {
        return $this->redirect($user->route(), navigate: true);
    }
}
