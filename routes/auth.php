<?php

use App\Livewire\Auth\EmailVerification;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Password\Confirm;
use App\Livewire\Auth\Password\Email;
use App\Livewire\Auth\Password\Reset;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Verify;
use App\Livewire\Clan;
use App\Livewire\Notification;
use App\Livewire\Option;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)
        ->name('login');

    Route::get('register', Register::class)
        ->name('register');
});

Route::get('password/reset', Email::class)
    ->name('password.request');

Route::get('password/reset/{token}', Reset::class)
    ->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('email/verify', Verify::class)
        ->name('verification.notice');

    Route::get('password/confirm', Confirm::class)
        ->name('password.confirm');

    Route::get('email/verify/{id}/{hash}', EmailVerification::class)
        ->middleware('signed')
        ->name('verification.verify');

    Route::get('/options', Option::class)->name('options.create');

    Route::get('/clan', Clan::class)->name('clan.settings');

    Route::get('/notifications', Notification::class)->name('notifications.index');
});
