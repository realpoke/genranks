<?php

use App\Http\Controllers\GenLinkDownloadController;
use App\Http\Controllers\MapLinkDownloadController;
use App\Livewire\Clan\ShowClan;
use App\Livewire\Game;
use App\Livewire\Landing;
use App\Livewire\Leaderboard;
use App\Livewire\Map;
use App\Livewire\Markdown;
use App\Livewire\Profile;
use App\Livewire\ShowGame;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class)->name('home');

Route::get('/doc/{markdown}', Markdown::class)->name('markdown.show');

Route::get('/profile/{user}', Profile::class)->name('profile.show');

Route::get('/maps', Map::class)->name('map.index');
Route::get('/map/download/{map}', MapLinkDownloadController::class)
    ->name('map.download')
    ->middleware('signed');

Route::get('/clan/{clan}', ShowClan::class)->name('clan.show');

Route::get('/games', Game::class)->name('game.index');
Route::get('/game/{game}', ShowGame::class)->name('game.show');
Route::redirect('/game', '/games');

Route::get('/leaderboard', Leaderboard::class)->name('leaderboard.index');

Route::redirect('/discord', 'https://discord.com/users/123792843851431937')->name('discord');

Route::get('/genlink/download', GenLinkDownloadController::class)->name('genlink.download');
