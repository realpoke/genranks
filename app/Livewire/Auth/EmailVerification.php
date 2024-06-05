<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\VerifyEmailContract;
use App\Livewire\Forms\Auth\EmailVerificationForm;
use Livewire\Component;

class EmailVerification extends Component
{
    public EmailVerificationForm $form;

    public function mount(string $id, string $hash, VerifyEmailContract $verifier)
    {
        $this->form->id = $id;
        $this->form->hash = $hash;

        $verifier($this->form);
    }
}
