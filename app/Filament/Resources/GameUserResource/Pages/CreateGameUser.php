<?php

namespace App\Filament\Resources\GameUserResource\Pages;

use App\Filament\Resources\GameUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGameUser extends CreateRecord
{
    protected static string $resource = GameUserResource::class;
}
