<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\AuthenticatesUserContract;
use App\Livewire\Forms\Auth\LoginForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser implements AuthenticatesUserContract
{
    use WithLimits;

    public function __invoke(LoginForm $form): bool
    {
        $this->limitTo(5, 'form.email', 'authenticate');

        if (! Auth::attempt(['email' => $form->email, 'password' => $form->password], $form->remember)) {
            $form->password = '';

            return false;
        }

        $this->clearRateLimiter();

        return true;
    }
}
