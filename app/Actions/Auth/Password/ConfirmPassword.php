<?php

namespace App\Actions\Auth\Password;

use App\Contracts\Auth\Password\ConfirmsPasswordContract;
use App\Livewire\Forms\Auth\Password\ConfirmForm;

class ConfirmPassword implements ConfirmsPasswordContract
{
    public function __invoke(ConfirmForm $form)
    {
        $form->validate();

        session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('home'));
    }
}
