<?php

namespace App\Contracts\Auth\Password;

use App\Livewire\Forms\Auth\Password\ConfirmForm;

interface ConfirmsPasswordContract
{
    public function __invoke(ConfirmForm $form);
}
