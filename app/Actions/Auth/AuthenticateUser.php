<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\AuthenticatesUserContract;
use App\Livewire\Forms\Auth\LoginForm;
use App\Traits\withLimits;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser implements AuthenticatesUserContract
{
    use withLimits;

    public function authenticate(LoginForm $form): bool
    {
        $this->limitTo(5, 'form.email', 'authenticate', 60);
        if (! Auth::attempt(['email' => $form->email, 'password' => $form->password], $form->remember)) {
            $form->password = '';

            return false;
        }
        $this->clearRateLimiter();

        return true;
    }
}
