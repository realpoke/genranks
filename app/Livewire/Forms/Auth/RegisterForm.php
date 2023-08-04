<?php

namespace App\Livewire\Forms\Auth;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class RegisterForm extends Form
{
    use AuthRules;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms = false;

    public function rules(): array
    {
        return [
            'name' => AuthRules::nameRules(),
            'email' => AuthRules::emailRules(),
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
