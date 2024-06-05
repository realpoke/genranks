<?php

namespace App\Contracts\Auth\Password;

use App\Livewire\Forms\Auth\Password\EmailForm;

interface SendsPasswordResetLinkContract
{
    public function __invoke(EmailForm $form);
}
