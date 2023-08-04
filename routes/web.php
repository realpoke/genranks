<?php

use App\Livewire\Landing;
use App\Livewire\Markdown;
use App\Livewire\Option;
use App\Livewire\Profile;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Landing::class)->name('home');

Route::get('/doc/{markdown}', Markdown::class)->name('markdown.show');

Route::get('/profile/{user}', Profile::class)->name('profile.show');
Route::middleware(['auth'])->group(function () {
    Route::get('/options', Option::class)->name('options.create');
});
