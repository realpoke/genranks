<?php

namespace App\Livewire\Partials;

use App\Contracts\Auth\LogoutUserContract;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NavigationMenu extends Component
{
    public ?string $name;

    public ?string $email;

    #[On('update-navigation-name')]
    public function updateName($name)
    {
        $this->name = $name;
    }

    #[On('update-navigation-email')]
    public function updateEmail($email)
    {
        $this->email = $email;
    }

    public function logout(LogoutUserContract $logouter)
    {
        $loggedOut = $logouter();

        $this->redirectRoute('home', navigate: true);
    }

    public function mount()
    {
        $this->name = $this->user()?->name;
        $this->email = $this->user()?->email;
    }

    #[Computed]
    protected function user()
    {
        return Auth::user();
    }
}
