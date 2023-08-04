<?php

namespace App\Livewire\Forms\Auth\Option;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class UpdatePasswordForm extends Form
{
    use AuthRules;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'current_password' => AuthRules::currentPasswordRules(),
            'password' => AuthRules::passwordRules(),
            'password_confirmation' => AuthRules::passwordConfirmationRules(),
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'curren password',
            'password_confirmation' => 'password confirmation',
        ];
    }
}
