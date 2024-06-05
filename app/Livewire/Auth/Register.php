<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\CreatesNewUserContract;
use App\Livewire\Forms\Auth\RegisterForm;
use App\Traits\FormAttributes;
use App\Traits\withLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    use FormAttributes, withLimits;

    public RegisterForm $form;

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function updatedFormPasswordConfirmation()
    {
        $this->validateOnly('form.password');
    }

    public function register(CreatesNewUserContract $creator)
    {
        $this->limitTo(10, 'form.email', 'register');

        $creator($this->form);

        return redirect()->intended(route('home'));
    }
}
