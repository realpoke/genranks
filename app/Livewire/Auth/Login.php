<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\AuthenticatesUserContract;
use App\Livewire\Forms\Auth\LoginForm;
use App\Traits\FormAttributes;
use App\Traits\WithLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    use FormAttributes, WithLimits;

    public LoginForm $form;

    public function login(AuthenticatesUserContract $authenticator)
    {
        $this->limitTo(10, 'form.email', 'log in');

        if ($authenticator($this->form)) {
            return $this->redirectIntended(route('home'), navigate: true);
        }

        $this->addError('form.email', __('auth.failed'));
    }
}
