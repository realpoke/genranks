<?php

namespace App\Livewire\Forms\Clan;

use App\Traits\Rules\ClanRules;
use Livewire\Form;

class CreateClanForm extends Form
{
    use ClanRules;

    public string $name = '';

    public string $tag = '';

    public string $description = '';

    public function rules(): array
    {
        return [
            'name' => ClanRules::nameRules(),
            'tag' => ClanRules::tagRules(),
            'description' => ClanRules::descriptionRules(),
        ];
    }
}
