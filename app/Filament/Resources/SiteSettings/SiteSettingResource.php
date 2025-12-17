<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\CreateSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Models\SiteSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|null $navigationGroup = "Контент";

    protected static ?string $modelLabel = "Настройка"; // Изменено на единственное число для ясности

    protected static ?string $pluralModelLabel = "Настройки сайта"; // Изменено

    protected static ?string $navigationIcon = "heroicon-o-wrench-screwdriver";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Параметр настройки')
                    ->schema([
                        TextInput::make('key')
                            ->label('Ключ настройки (KEY)')
                            ->required()
                            ->maxLength(255)
                            // Если мы редактируем, делаем поле только для чтения,
                            // чтобы избежать поломки кода при переименовании ключа.
                            ->disabled(fn (?SiteSetting $record) => $record !== null)
                            ->dehydrated(fn (?SiteSetting $record) => $record === null) // Обязательно для создания
                            ->unique(ignoreRecord: true)
                            ->hint('Ключ, используемый в коде (не должен меняться после создания)'),

                        Textarea::make('value')
                            ->label('Значение настройки (VALUE)')
                            ->rows(3)
                            ->nullable()
                            ->default('')
                            ->maxLength(65535)
                            ->hint('Значение, которое будет возвращено при вызове этого ключа.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Ключ настройки')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                
                TextColumn::make('value')
                    ->label('Значение')
                    ->limit(70)
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // Здесь обычно не нужны массовые действия для настроек
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
            'index'  => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit'   => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}