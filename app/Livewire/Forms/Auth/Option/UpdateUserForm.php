<?php

namespace App\Livewire\Forms\Auth\Option;

use App\Models\User;
use App\Traits\Rules\AuthRules;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Form;

class UpdateUserForm extends Form
{
    use AuthRules;

    public string $name = '';

    public string $email = '';

    #[Locked]
    public User $user;

    public function rules(): array
    {
        return [
            'name' => AuthRules::nameRules(),
            'email' => AuthRules::emailRules(Auth::user()),
        ];
    }
}
