<?php

namespace App\Livewire\Auth\Option;

use App\Contracts\Auth\Option\UpdatesUserContract;
use App\Livewire\Forms\Auth\Option\UpdateUserForm;
use App\Livewire\Partials\NavigationMenu;
use App\Traits\FormAttributes;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UpdateUser extends Component
{
    use FormAttributes, WithLimits;

    public UpdateUserForm $form;

    public function mount()
    {
        $this->form->user = $this->user();
        $this->form->name = $this->user()->name;
        $this->form->email = $this->user()->email;
    }

    public function updateUser(UpdatesUserContract $updater)
    {
        $this->limitTo(10, 'form.email', 'update your profile');

        $updater($this->form);

        $this->dispatch('option-user-saved');
        $this->dispatch('update-navigation-name', $this->form->name)->to(NavigationMenu::class);
        $this->dispatch('update-navigation-email', $this->form->email)->to(NavigationMenu::class);
    }

    public function sendEmailVerification()
    {
        $this->limitTo(2, 'form.email', 'resend email');

        $this->user()->sendEmailVerificationNotification();

        $this->dispatch('verification-link-sent');
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }
}
