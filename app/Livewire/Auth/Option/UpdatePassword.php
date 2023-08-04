<?php

namespace App\Livewire\Auth\Option;

use App\Contracts\Auth\Option\UpdatesPasswordContract;
use App\Livewire\Forms\Auth\Option\UpdatePasswordForm;
use App\Traits\FormAttributes;
use App\Traits\withLimits;
use Livewire\Component;

class UpdatePassword extends Component
{
    use FormAttributes, withLimits;

    public UpdatePasswordForm $form;

    public function updatePassword(UpdatesPasswordContract $updater)
    {
        $this->limitTo(10, 'form.current_password', 'update your password');

        $updater->update($this->form);

        $this->dispatch('option-password-saved');
    }
}
