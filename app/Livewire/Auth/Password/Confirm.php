<?php

namespace App\Livewire\Auth\Password;

use App\Contracts\Auth\Password\ConfirmsPasswordContract;
use App\Livewire\Forms\Auth\Password\ConfirmForm;
use App\Traits\FormAttributes;
use App\Traits\WithLimits;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Confirm extends Component
{
    use FormAttributes, WithLimits;

    public ConfirmForm $form;

    public function confirmPassword(ConfirmsPasswordContract $confirmer)
    {
        $this->limitTo(10, 'password', 'confirm password');

        $confirmer($this->form);
    }
}
