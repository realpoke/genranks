<?php

namespace App\Actions\Auth\Option;

use App\Contracts\Auth\Option\UpdatesPasswordContract;
use App\Livewire\Forms\Auth\Option\UpdatePasswordForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePassword implements UpdatesPasswordContract
{
    public function __invoke(UpdatePasswordForm $form)
    {
        $form->validate();

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_'.Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
            ]);
        }

        Auth::user()->forceFill([
            'password' => Hash::make($form->password),
        ])->save();

        $form->reset();
    }
}
