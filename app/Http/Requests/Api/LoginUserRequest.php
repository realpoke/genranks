<?php

namespace App\Http\Requests\Api;

use App\Traits\Rules\AuthRules;
use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    use AuthRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => AuthRules::loginKeyRules(),
            'password' => AuthRules::loginPasswordRules(),
            'token' => AuthRules::tokenRules(),
        ];
    }
}
