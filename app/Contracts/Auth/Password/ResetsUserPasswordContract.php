<?php

namespace App\Contracts\Auth\Password;

use App\Livewire\Forms\Auth\Password\ResetForm;

interface ResetsUserPasswordContract
{
    public function reset(ResetForm $form);
}
