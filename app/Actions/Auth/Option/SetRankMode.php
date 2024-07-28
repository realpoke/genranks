<?php

namespace App\Actions\Auth\Option;

use App\Contracts\Auth\Option\SetsRankModeContract;
use App\Livewire\Forms\Auth\Option\RankModeForm;
use Illuminate\Support\Facades\Auth;

class SetRankMode implements SetsRankModeContract
{
    public function __invoke(RankModeForm $form)
    {
        $form->validate();

        Auth::user()->rank_mode = $form->mode;
        Auth::user()->save();
    }
}
