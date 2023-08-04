<?php

namespace App\Contracts\Auth;

use App\Livewire\Forms\Auth\EmailVerificationForm;

interface VerifiesEmailContract
{
    public function verify(EmailVerificationForm $form);
}
