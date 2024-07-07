<?php

namespace App\Actions\Clan;

use App\Contracts\Clan\CreatesClanContract;
use App\Livewire\Forms\Clan\CreateClanForm;
use App\Models\Clan;
use Illuminate\Support\Facades\Auth;

class CreateClan implements CreatesClanContract
{
    public function __invoke(CreateClanForm $form): ?Clan
    {
        $form->validate();

        return Auth::user()->createClan(
            $form->name,
            $form->tag,
            $form->description
        );
    }
}
