<?php

namespace App\Livewire\Forms\Auth\Option;

use Livewire\Form;

class TokenForm extends Form
{
    public $confirmingApiTokenDeletion = false;

    public $apiTokenBeingDeleted;
}
