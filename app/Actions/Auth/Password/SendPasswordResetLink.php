<?php

namespace App\Actions\Auth\Password;

use App\Contracts\Auth\Password\SendsPasswordResetLinkContract;
use App\Livewire\Forms\Auth\Password\EmailForm;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLink implements SendsPasswordResetLinkContract
{
    public function __invoke(EmailForm $form)
    {
        $form->validate();

        $response = Password::broker()->sendResetLink(['email' => $form->email]);

        if ($response == Password::RESET_LINK_SENT) {
            $form->emailSentMessage = __($response);

            return null;
        }

        return $response;
    }
}
