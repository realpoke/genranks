<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use App\Traits\Rules\PermissionRules;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PermissionsRelationManager extends RelationManager
{
    use PermissionRules;

    protected static string $relationship = 'permissions';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->rules(PermissionRules::nameRules($form->getModelInstance())),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(auth()->user()->can(['set:permission', 'create:permission'])),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->visible(auth()->user()->can(['set:permission', 'create:permission'])),
            ]);
    }
}
