<?php

namespace App\Livewire\Forms\Auth\Option;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class DeleteUserForm extends Form
{
    public bool $confirmingUserDeletion = false;

    public string $password = '';

    public function rules(): array
    {
        return [
            'password' => AuthRules::currentPasswordRules(),
        ];
    }
}
