<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
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


    public static function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\Section::make('Основная информация')
                ->columns(2)
                ->schema([
                    FileUpload::make('avatar')
                        ->image()
                        ->avatar()
                        ->directory('avatars')
                        ->disk('public')
                        ->visibility('public'),

                    TextInput::make('email')->email()->required(),

                    TextInput::make('name')->required(),
                    TextInput::make('last_name'),
                    DatePicker::make('birth_date'),
                    Select::make('gender')
                        ->options(['male' => 'Муж', 'female' => 'Жен']),
                    TextInput::make('phone'),
                ]),

            \Filament\Forms\Components\Section::make('Доступ')
                ->columns(2)
                ->schema([
                    Select::make('role_id')
                        ->relationship('role', 'name')
                        ->required()
                        ->reactive(), // ← важно! чтобы реагировать на смену роли
                ]),

            // ←←← ПОЛЯ ТРЕНЕРА — видны ТОЛЬКО если выбрана роль "trainer"
            \Filament\Forms\Components\Section::make('Информация о тренере')
                ->visible(
                    fn(Get $get) =>
                    \App\Models\Role::find($get('role_id'))?->name === 'trainer'
                )
                ->columns(2)
                ->schema([
                    Textarea::make('bio')
                        ->rows(4)
                        ->placeholder('Расскажите о себе...'),

                    TagsInput::make('specialties')
                        ->label('Специализации')
                        ->placeholder('Йога, Пилатес, TRX...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'U')),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('role.name')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'admin' => 'danger',
                        'trainer' => 'warning',
                        'client' => 'success',
                        default => 'gray',
                    }),

                \Filament\Tables\Columns\TextColumn::make('forms_count')
                    ->label('Занятий')
                    ->counts('forms')
                    ->sortable(),
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
