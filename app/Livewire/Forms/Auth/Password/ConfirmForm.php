<?php

namespace App\Livewire\Forms\Auth\Password;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class ConfirmForm extends Form
{
    use AuthRules;

    public string $password = '';

    public function rules(): array
    {
        return [
            'password' => AuthRules::currentPasswordRules(),
        ];
    }
}
