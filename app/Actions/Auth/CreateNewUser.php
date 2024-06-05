<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\CreatesNewUserContract;
use App\Livewire\Forms\Auth\RegisterForm;
use App\Models\User;
use App\Traits\WithLimits;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class CreateNewUser implements CreatesNewUserContract
{
    use WithLimits;

    public function __invoke(RegisterForm $form)
    {
        $form->validate();

        $this->limitTo(2, 'form.email', 'create');

        $user = User::create([
            'email' => $form->email,
            'name' => $form->name,
            'password' => $form->password,
        ]);

        event(new Registered($user));

        Auth::login($user, true);
    }
}
