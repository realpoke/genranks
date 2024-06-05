<?php

namespace App\Livewire\Auth\Option;

use App\Contracts\Auth\Option\DeletesTokenContract;
use App\Livewire\Forms\Auth\Option\TokenForm;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Component;

class TokenManager extends Component
{
    public TokenForm $form;

    public function confirmApiTokenDeletion(PersonalAccessToken $token)
    {
        $this->form->confirmingApiTokenDeletion = true;
        $this->form->apiTokenBeingDeleted = $token;
    }

    public function deleteApiToken(DeletesTokenContract $deleter)
    {
        $deleter($this->form);

        $this->dispatch('api-token-deleted');
    }
}
