<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\AuthenticatesUserContract;
use App\Livewire\Forms\Auth\LoginForm;
use App\Traits\FormAttributes;
use App\Traits\withLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    use FormAttributes, withLimits;

    public LoginForm $form;

    public function login(AuthenticatesUserContract $authenticator)
    {
        $this->limitTo(10, 'form.email', 'log in');

        if ($authenticator->authenticate($this->form)) {
            return redirect()->intended(route('home'));
        }

        $this->addError('form.email', __('auth.failed'));
    }
}
