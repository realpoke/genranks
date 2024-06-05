<?php

namespace App\Livewire\Auth\Password;

use App\Contracts\Auth\Password\SendsPasswordResetLinkContract;
use App\Livewire\Forms\Auth\Password\EmailForm;
use App\Traits\FormAttributes;
use App\Traits\WithLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Email extends Component
{
    use FormAttributes, WithLimits;

    public EmailForm $form;

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function sendResetPasswordLink(SendsPasswordResetLinkContract $sender)
    {
        $this->limitTo(2, 'form.email', 'resend email');

        $response = $sender($this->form);

        if ($response != null) {
            $this->addError('form.email', __($response));
        }
    }
}
