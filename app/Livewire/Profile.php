<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Profile extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }
}
