<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\Option\DeleteUserForm;

interface DeletesUserContract
{
    public function __invoke(DeleteUserForm $form);
}
