<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\CreateSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|null $navigationGroup = "Контент";

    protected static ?string $modelLabel = "Настройки";

    protected static ?string $pluralModelLabel = "Настройки";

    protected static ?string $navigationIcon = "heroicon-o-wrench-screwdriver";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Добавьте поля формы здесь при необходимости
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Добавьте колонки таблицы здесь при необходимости
            ])
            ->actions([
                // Добавьте действия здесь при необходимости
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
            'index' => ListSiteSettings::route('/'),
            'create' => CreateSiteSetting::route('/create'),
            'edit' => EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
