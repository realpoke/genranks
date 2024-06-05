<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\Option\TokenForm;

interface DeletesTokenContract
{
    public function __invoke(TokenForm $form);
}
