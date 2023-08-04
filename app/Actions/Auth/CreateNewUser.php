<?php

namespace App\Actions\Auth;

use App\Contracts\Auth\CreatesNewUserContract;
use App\Livewire\Forms\Auth\RegisterForm;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class CreateNewUser implements CreatesNewUserContract
{
    public function create(RegisterForm $form)
    {
        $form->validate();

        $user = User::create([
            'email' => $form->email,
            'name' => $form->name,
            'password' => $form->password,
        ]);

        event(new Registered($user));

        Auth::login($user, true);
    }
}
