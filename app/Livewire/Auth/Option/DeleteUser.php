<?php

namespace App\Livewire\Auth\Option;

use App\Contracts\Auth\Option\DeletesUserContract;
use App\Livewire\Forms\Auth\Option\DeleteUserForm;
use App\Traits\FormAttributes;
use Livewire\Component;

class DeleteUser extends Component
{
    use FormAttributes;

    public DeleteUserForm $form;

    public function confirmUserDeletion()
    {
        $this->resetErrorBag();
        $this->form->password = '';

        $this->dispatch('confirming-delete-user');
        $this->form->confirmingUserDeletion = true;
    }

    public function deleteUser(DeletesUserContract $deleter)
    {
        $deleter($this->form);

        return $this->redirect(route('home'));
    }
}
