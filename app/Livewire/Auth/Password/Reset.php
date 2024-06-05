<?php

namespace App\Livewire\Auth\Password;

use App\Contracts\Auth\Password\ResetsUserPasswordContract;
use App\Livewire\Forms\Auth\Password\ResetForm;
use App\Traits\FormAttributes;
use App\Traits\WithLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Reset extends Component
{
    use FormAttributes, WithLimits;

    public ResetForm $form;

    public function mount($token)
    {
        $this->form->email = request()->query('email', '');
        $this->form->token = $token;
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function resetPassword(ResetsUserPasswordContract $resetter)
    {
        $this->limitTo(5, 'form.email', 'reset password');

        $response = $resetter($this->form);

        if ($response != null) {
            $this->addError('form.email', __($response));
        }
    }
}
