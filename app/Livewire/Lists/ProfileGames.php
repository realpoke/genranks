<?php

namespace App\Livewire\Lists;

use App\Livewire\DynamicList;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class ProfileGames extends DynamicList
{
    protected $itemView = 'items.games';

    #[Locked]
    public User $user;

    #[Computed()]
    protected function getModel(): Model
    {
        return new (Game::class);
    }

    protected function setupExtraFilters(): void
    {
        $this->addFilter(function ($query) {
            return $query->whereHas('users', function ($query) {
                $query->where('users.id', $this->passthrough['userid']);
            });
        });
    }
}
