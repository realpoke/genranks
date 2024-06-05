<?php

namespace App\Contracts\Auth;

use App\Livewire\Forms\Auth\LoginForm;

interface AuthenticatesUserContract
{
    public function __invoke(LoginForm $form): bool;
}
