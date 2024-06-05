<?php

namespace App\Actions\Auth\Option;

use App\Contracts\Auth\Option\DeletesUserContract;
use App\Livewire\Forms\Auth\Option\DeleteUserForm;
use Illuminate\Support\Facades\Auth;

class DeleteUser implements DeletesUserContract
{
    public function __invoke(DeleteUserForm $form)
    {
        $form->validate();

        Auth::user()->tokens->each->delete();
        Auth::user()->delete();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }
}
