<?php

use App\Livewire\Game;
use App\Livewire\Landing;
use App\Livewire\Markdown;
use App\Livewire\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class)->name('home');

Route::get('/doc/{markdown}', Markdown::class)->name('markdown.show');

Route::get('/profile/{user}', Profile::class)->name('profile.show');

Route::get('/games', Game::class)->name('game.index');
