<?php

namespace App\Filament\Resources\SectionSettings\Pages;

use App\Filament\Resources\SectionSettings\SectionSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSectionSetting extends EditRecord
{
    protected static string $resource = SectionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
