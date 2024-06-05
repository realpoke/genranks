<?php

namespace App\Actions\Auth\Option;

use App\Contracts\Auth\Option\UpdatesUserContract;
use App\Livewire\Forms\Auth\Option\UpdateUserForm;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UpdateUser implements UpdatesUserContract
{
    public function __invoke(UpdateUserForm $form)
    {
        $form->validate();

        if (
            $form->email !== $form->user->email &&
            $form->user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($form);
        } else {
            $form->user->forceFill([
                'name' => $form->name,
                'email' => $form->email,
            ])->save();
        }
    }

    private function updateVerifiedUser(UpdateUserForm $form)
    {
        $form->user->forceFill([
            'name' => $form->name,
            'email' => $form->email,
            'email_verified_at' => null,
        ])->save();
    }
}
