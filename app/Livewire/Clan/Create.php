<?php

namespace App\Livewire\Clan;

use App\Contracts\Clan\CreatesClanContract;
use App\Livewire\Forms\Clan\CreateClanForm;
use App\Traits\FormAttributes;
use App\Traits\WithLimits;
use Livewire\Component;

class Create extends Component
{
    use FormAttributes, WithLimits;

    public CreateClanForm $form;

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function createClan(CreatesClanContract $creator)
    {
        $this->limitTo(10, 'form.name', 'create a clan');
        $clan = $creator($this->form);

        if (! is_null($clan)) {
            return $this->redirectIntended(route('clan.show', $clan), navigate: true);
        }
    }
}
