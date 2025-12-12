<?php

namespace App\Filament\Admin\Resources\GroupClasses\Pages;

use App\Filament\Admin\Resources\GroupClasses\GroupClassResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGroupClasses extends ListRecords
{
    protected static string $resource = GroupClassResource::class;

    protected function getActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
