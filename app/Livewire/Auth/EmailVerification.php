<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\VerifiesEmailContract;
use App\Livewire\Forms\Auth\EmailVerificationForm;
use Livewire\Component;

class EmailVerification extends Component
{
    public EmailVerificationForm $form;

    public function mount(string $id, string $hash, VerifiesEmailContract $verifier)
    {
        $this->form->id = $id;
        $this->form->hash = $hash;

        $verifier->verify($this->form);
    }
}
