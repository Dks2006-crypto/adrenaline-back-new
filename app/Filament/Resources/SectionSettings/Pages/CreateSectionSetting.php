<?php

namespace App\Filament\Resources\SectionSettings\Pages;

use App\Filament\Resources\SectionSettings\SectionSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSectionSetting extends CreateRecord
{
    protected static string $resource = SectionSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
