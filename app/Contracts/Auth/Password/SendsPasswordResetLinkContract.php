<?php

namespace App\Contracts\Auth\Password;

use App\Livewire\Forms\Auth\Password\EmailForm;

interface SendsPasswordResetLinkContract
{
    public function send(EmailForm $form);
}
