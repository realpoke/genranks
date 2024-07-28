<?php

namespace App\Livewire;

use App\Enums\RankBracket;
use App\Enums\Side;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Profile extends Component
{
    use WithPagination;

    public User $user;

    public string $profilePicture;

    public Side $favoriteSide;

    public RankBracket $bracket;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->favoriteSide = Side::favoriteSide($this->user->stats['Sides'] ?? []);
        $this->bracket = RankBracket::getRankBracketByElo($this->user->elo);
        $this->profilePicture = $this->user->pictureUrl();
    }

    public function render()
    {
        return view('livewire.profile', [
            'games' => $this->user->games()->latest()->paginate(12),
        ]);
    }
}
