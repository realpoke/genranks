<?php

namespace App\Contracts\Auth\Option;

use App\Livewire\Forms\Auth\TwoFactorForm;

interface UsesTwoFactorContract
{
    public function confirm(TwoFactorForm $form);

    public function disable(TwoFactorForm $form);

    public function enable(TwoFactorForm $form);

    public function generate(TwoFactorForm $form);
}
