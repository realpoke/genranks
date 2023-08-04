<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\Option\DeleteUserForm;

interface DeletesUserContract
{
    public function delete(DeleteUserForm $form);
}
