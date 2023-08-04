<?php

namespace App\Livewire\Forms\Auth\Password;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class ResetForm extends Form
{
    use AuthRules;

    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms = false;

    public function rules(): array
    {
        return [
            'token' => AuthRules::tokenRules(),
            'email' => AuthRules::resetEmailRules(),
            'password' => AuthRules::passwordRules(),
            'password_confirmation' => AuthRules::passwordConfirmationRules(),
            'terms' => AuthRules::termsRules(),
        ];
    }

    public function attributes(): array
    {
        return [
            'terms' => 'terms and conditions',
            'password_confirmation' => 'password confirmation',
        ];
    }
}
