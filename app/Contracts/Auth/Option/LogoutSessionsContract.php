<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\Option\LogoutSessionsForm;

interface LogoutSessionsContract
{
    public function logout(LogoutSessionsForm $form);

    public function isDatabaseSession(): bool;
}
