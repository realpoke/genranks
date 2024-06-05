<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\Option\UpdateUserForm;

interface UpdatesUserContract
{
    public function __invoke(UpdateUserForm $form);
}
