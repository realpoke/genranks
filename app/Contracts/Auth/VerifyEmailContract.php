<?php

namespace App\Contracts\Auth;

use App\Livewire\Forms\Auth\EmailVerificationForm;

interface VerifyEmailContract
{
    public function __invoke(EmailVerificationForm $form);
}
