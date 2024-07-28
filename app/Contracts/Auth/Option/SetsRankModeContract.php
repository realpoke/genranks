<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\Option\RankModeForm;

interface SetsRankModeContract
{
    public function __invoke(RankModeForm $form);
}
