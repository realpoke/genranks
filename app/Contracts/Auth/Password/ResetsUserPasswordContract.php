<?php

namespace App\Contracts\Auth\Password;

use App\Livewire\Forms\Auth\Password\ResetForm;

interface ResetsUserPasswordContract
{
    public function __invoke(ResetForm $form);
}
