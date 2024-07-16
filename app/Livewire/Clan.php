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
        // TODO: Add clan invites, accept invites, decline invites, block invites
        // If in a clan, create a leave clan button
        // Show blocked clans and a button to unblock
        // Add clans to blocklist by searching for clans
        // If owning a clan, create a invite section, transfer clan section and delete clan section
        // Show clan members, if owning can kick members
        // If owning, can edit clan name, tag, description
        $this->canCreateClan = $this->user()?->canCreateClan();

        return view('livewire.clan');
    }

    #[Computed()]
    public function user()
    {
        return Auth::user();
    }
}
