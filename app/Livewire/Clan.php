<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Clan extends Component
{
    public ?bool $canCreateClan;

    public function render()
    {
        $this->canCreateClan = $this->user()?->canCreateClan();

        return view('livewire.clan');
    }

    #[Computed()]
    public function user()
    {
        return Auth::user();
    }
}
