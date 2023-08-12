<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Traits\Rules\AuthRules;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    use AuthRules;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    protected static ?string $navigationGroup = 'Moderator Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->rules(AuthRules::nameRules()),
                TextInput::make('nickname')
                    ->rules(AuthRules::nicknameRules($form->getModelInstance())),
                TextInput::make('rank')
                    ->numeric()
                    ->maxValue(16777215)
                    ->minValue(1),
                TextInput::make('monthly_rank')
                    ->numeric()
                    ->maxValue(16777215)
                    ->minValue(1),
                TextInput::make('elo')
                    ->numeric()
                    ->maxValue(32767)
                    ->minValue(1),
                TextInput::make('monthly_elo')
                    ->numeric()
                    ->maxValue(32767)
                    ->minValue(1),
                TextInput::make('email')
                    ->rules(AuthRules::emailRules($form->getModelInstance())),
                DateTimePicker::make('email_verified_at')
                    ->nullable()
                    ->seconds(false)
                    ->native(false)
                    ->weekStartsOnMonday(),
                TextInput::make('password')
                    ->password()
                    ->rules(AuthRules::passwordRules())
                    ->visibleOn('create'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->rules(AuthRules::passwordConfirmationRules())
                    ->visibleOn('create'),
                CheckboxList::make('roles')
                    ->columnSpanFull()
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->visible(auth()->user()->can('set:role')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                IconColumn::make('email_verified_at')->label('Verified')->boolean(),
                TextColumn::make('roles.name'),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
