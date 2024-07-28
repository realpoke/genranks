<?php

namespace App\Livewire\Auth\Option;

use App\Contracts\Auth\Option\SetsRankModeContract;
use App\Enums\RankMode as EnumsRankMode;
use App\Livewire\Forms\Auth\Option\RankModeForm;
use App\Traits\FormAttributes;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RankMode extends Component
{
    use FormAttributes, WithLimits;

    public RankModeForm $form;

    public array $modes;

    public function mount()
    {
        $this->modes = EnumsRankMode::values();
        $this->form->mode = $this->user()->rank_mode;
    }

    public function setRankMode(SetsRankModeContract $changer)
    {
        $this->limitTo(10, 'form.mode', 'change rank mode');

        $changer($this->form);

        $this->dispatch('rank-mode-saved');
    }

    #[Computed()]
    public function user()
    {
        return Auth::user();
    }
}
