<?php

namespace App\Filament\Resources\SectionSettings\Pages;

use App\Filament\Resources\SectionSettings\SectionSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSectionSettings extends ListRecords
{
    protected static string $resource = SectionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
