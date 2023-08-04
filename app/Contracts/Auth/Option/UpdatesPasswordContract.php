<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\Option\UpdatePasswordForm;

interface UpdatesPasswordContract
{
    public function update(UpdatePasswordForm $form);
}
