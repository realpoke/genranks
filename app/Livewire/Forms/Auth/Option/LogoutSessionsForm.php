<?php

namespace App\Livewire\Forms\Auth\Option;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class LogoutSessionsForm extends Form
{
    use AuthRules;

    public bool $confirmingLogout = false;

    public string $password = '';

    public function rules(): array
    {
        return [
            'password' => AuthRules::currentPasswordRules(),
        ];
    }
}
