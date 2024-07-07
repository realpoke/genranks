<?php

namespace App\Contracts\Clan;

use App\Livewire\Forms\Clan\CreateClanForm;
use App\Models\Clan;

interface CreatesClanContract
{
    public function __invoke(CreateClanForm $form): ?Clan;
}
