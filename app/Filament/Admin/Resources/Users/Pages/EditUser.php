<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\Role;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Если роль изменилась на тренера, инициализируем поля
        if (isset($data['role_id']) && Role::find($data['role_id'])?->name === 'trainer') {
            $data['bio'] = $data['bio'] ?? '';
            $data['specialties'] = $data['specialties'] ?? [];
        }

        return $data;
    }
}
