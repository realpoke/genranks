<?php

namespace App\Contracts\Auth;

use App\Livewire\Forms\Auth\RegisterForm;

interface CreatesNewUserContract
{
    public function create(RegisterForm $form);
}
