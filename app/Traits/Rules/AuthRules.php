<?php

namespace App\Traits\Rules;

use App\Enums\RankMode;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

trait AuthRules
{
    public static function loginKeyRules(): array
    {
        return ['required', 'string', 'email'];
    }

    public static function loginPasswordRules(): array
    {
        return ['required', 'string'];
    }

    public static function loginRememberRules(): array
    {
        return ['sometimes'];
    }

    public static function currentPasswordRules(): array
    {
        return ['required', 'string', 'current_password'];
    }

    public static function passwordRules(): array
    {
        return ['required', 'string', 'min:8', 'confirmed'];
    }

    public static function passwordConfirmationRules(): array
    {
        return ['required', 'string'];
    }

    public static function nameRules(): array
    {
        return ['required', 'string', 'min:3', 'max:255'];
    }

    public static function tokenRules(): array
    {
        return ['required', 'string'];
    }

    public static function resetEmailRules(): array
    {
        return ['required', 'string', 'email'];
    }

    public static function emailRules(?Model $model = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            ($model ?
                Rule::unique(User::class, 'email')->ignoreModel($model) :
                Rule::unique(User::class, 'email')),
        ];
    }

    public static function termsRules(): array
    {
        return ['accepted'];
    }

    public static function rankModeRules(): array
    {
        return ['required', 'rank_mode' => [Rule::enum(RankMode::class)]];
    }
}
