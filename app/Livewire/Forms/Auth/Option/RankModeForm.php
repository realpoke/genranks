<?php

namespace App\Livewire\Forms\Auth\Option;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class RankModeForm extends Form
{
    use AuthRules;

    public ?string $mode = null;

    public function rules(): array
    {
        return [
            'mode' => AuthRules::nameRules(),
        ];
    }
}
