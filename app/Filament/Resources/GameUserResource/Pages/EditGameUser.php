<?php

namespace App\Filament\Resources\GameUserResource\Pages;

use App\Filament\Resources\GameUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameUser extends EditRecord
{
    protected static string $resource = GameUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
