<?php

namespace App\Livewire\Forms\Auth\Password;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class EmailForm extends Form
{
    use AuthRules;

    public string $email = '';

    public ?string $emailSentMessage = null;

    public bool $terms = false;

    public function rules(): array
    {
        return [
            'email' => AuthRules::resetEmailRules(),
            'terms' => AuthRules::termsRules(),
        ];
    }

    public function attributes(): array
    {
        return [
            'terms' => 'terms and conditions',
        ];
    }
}
