<?php

namespace App\Actions\Auth\Password;

use App\Contracts\Auth\Password\ResetsUserPasswordContract;
use App\Livewire\Forms\Auth\Password\ResetForm;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetUserPassword implements ResetsUserPasswordContract
{
    public function __invoke(ResetForm $form)
    {
        $form->validate();

        $response = Password::broker()->reset(
            [
                'token' => $form->token,
                'email' => $form->email,
                'password' => $form->password,
            ],
            function ($user, $password) {
                $user->password = $password;

                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
                Auth::guard()->login($user);
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            session()->flash(__($response));

            redirect()->route('home');

            return null;
        }

        return $response;
    }
}
