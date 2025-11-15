<?php

namespace App\Filament\Admin\Resources\Users;

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Schemas\UserForm;
use App\Filament\Admin\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Пользователи';

    public static function getRecordTitle(?Model $record): ?string
{
    if (!$record) {
        return null;
    }

    return match (true) {
        $record instanceof User =>
            $record->name
                ? trim($record->name . ' ' . ($record->last_name ?? ''))
                : $record->email,

        default => $record->getKey(),
    };
}


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->nullable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->dehydrated(false),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name'),
                DatePicker::make('birth_date'),
                Select::make('gender')
                    ->options(['male' => 'Муж', 'female' => 'Жен', 'other' => 'Другое']),
                TextInput::make('phone'),
                Select::make('branch_id')
                    ->relationship('branch', 'name'),
                Select::make('role_id')
                    ->relationship('role', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->searchable(),
                TextColumn::make('name'),
                TextColumn::make('role.name')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'admin' => 'danger',
                        'trainer' => 'warning',
                        'client' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('branch.name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
