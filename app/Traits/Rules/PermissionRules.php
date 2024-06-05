<?php

namespace App\Traits\Rules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

trait PermissionRules
{
    public static function nameRules(Model $model): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:255',
            Rule::unique(Role::class, 'name')->ignoreModel($model),
        ];
    }
}
