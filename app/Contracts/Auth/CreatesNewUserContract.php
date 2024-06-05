<?php

namespace App\Contracts\Auth;

use App\Livewire\Forms\Auth\RegisterForm;

interface CreatesNewUserContract
{
    public function __invoke(RegisterForm $form);
}
