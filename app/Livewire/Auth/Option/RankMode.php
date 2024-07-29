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

    public string $modeText;

    public function updatedFormMode()
    {
        $this->setModeText();
    }

    public function mount()
    {
        $this->modes = EnumsRankMode::values();
        $this->form->mode = $this->user()->rank_mode;
        $this->setModeText();
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

    private function setModeText()
    {
        if ($this->form->mode == 'all') {
            $this->modeText = 'All your games will be counted as ranked. This is the default.';
        } elseif ($this->form->mode == 'balanced') {
            $this->modeText = 'Your games will be counted as ranked if they are balanced, where all players have elo within '.EnumsRankMode::MAX_ELO_DIFFERENCE_FOR_BALANCED.'. This is a premium feature.';
        } elseif ($this->form->mode == 'fun') {
            $this->modeText = 'Your games will be counted as unranked, you\'r rank and elo will not be updated.';
        }
    }
}
