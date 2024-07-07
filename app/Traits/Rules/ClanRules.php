<?php

namespace App\Traits\Rules;

use App\Models\Clan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

trait ClanRules
{
    public static function nameRules(?Model $model = null): array
    {
        return [
            'required',
            'string',
            'min:5',
            'max:255',
            $model ?
                Rule::unique(Clan::class, 'name')->ignoreModel($model) :
                Rule::unique(Clan::class, 'name'),
        ];
    }

    public static function tagRules(?Model $model = null): array
    {
        return [
            'required',
            'string',
            'min:2',
            'max:10',
            $model ?
                Rule::unique(Clan::class, 'tag')->ignoreModel($model) :
                Rule::unique(Clan::class, 'tag'),
        ];
    }

    public static function descriptionRules(): array
    {
        return ['string', 'max:1024'];
    }
}
