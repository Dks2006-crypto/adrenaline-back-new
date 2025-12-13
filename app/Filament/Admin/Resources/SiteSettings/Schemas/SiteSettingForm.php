<?php

namespace App\Filament\Admin\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Ключ')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($record) => $record?->exists),

                TextInput::make('value')
                    ->label('Значение')
                    ->required(),
            ]);
    }
}
