<?php

namespace App\Filament\Resources\GalleryPosts;

use App\Filament\Resources\GalleryPosts\Pages\CreateGalleryPost;
use App\Filament\Resources\GalleryPosts\Pages\EditGalleryPost;
use App\Filament\Resources\GalleryPosts\Pages\ListGalleryPosts;
use App\Models\GalleryPost;
use BackedEnum;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use UnitEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

class GalleryPostResource extends Resource
{
    protected static ?string $model = GalleryPost::class;

    protected static ?string $navigationLabel = 'Gallery секция';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $modelLabel = 'Gallery секция';

    protected static ?string $pluralModelLabel = 'Gallery секция';

    protected static string|null $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основная информация')
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subtitle')
                            ->label('Описание')
                            ->required()
                            ->maxLength(500),
                        FileUpload::make('image')
                            ->directory('gallery')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->required(),
                        Toggle::make('active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime(),
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
            'index' => ListGalleryPosts::route('/'),
            'create' => CreateGalleryPost::route('/create'),
            'edit' => EditGalleryPost::route('/{record}/edit'),
        ];
    }
}
