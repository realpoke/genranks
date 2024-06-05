<?php

namespace App\Actions\Auth\Option;

use App\Contracts\Auth\Option\DeletesTokenContract;
use App\Livewire\Forms\Auth\Option\TokenForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class DeleteToken implements DeletesTokenContract
{
    public function __invoke(TokenForm $form)
    {
        $this->user()->tokens()->where('id', $form->apiTokenBeingDeleted->id)->first()->delete();
        $this->user()->load('tokens');

        $form->reset();
    }

    #[Computed]
    private function user()
    {
        return Auth::user();
    }
}
