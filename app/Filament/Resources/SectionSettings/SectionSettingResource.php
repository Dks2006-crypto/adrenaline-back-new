<?php

namespace App\Filament\Resources\SectionSettings;

use App\Filament\Resources\SectionSettings\Pages\EditSectionSetting;
use App\Models\SectionSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use UnitEnum;

class SectionSettingResource extends Resource
{
    protected static ?string $model = SectionSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Hero секция';
    protected static string|null $navigationGroup = 'Контент';
    protected static ?string $modelLabel = 'Hero секция';
    protected static ?string $pluralModelLabel = 'Hero секции';

    public static function form(Form $form): Form
    {
        return $form->schema([
                FormSection::make('Текст Hero секции')
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(500)
                            ->helperText('Основной заголовок секции'),
                        Textarea::make('description')
                            ->label('Описание')
                            ->required()
                            ->rows(8)
                            ->helperText('Текст с преимуществами и описанием клуба'),
                    ]),

                FormSection::make('Фоновая картинка')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Фоновая картинка')
                            ->image()
                            ->directory('hero')
                            ->disk('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->helperText('Максимальный размер: 5MB. Рекомендуемый размер: 1920x1080px'),
                    ]),

                FormSection::make('Настройки отображения')
                    ->schema([
                        TextInput::make('extra_data.background_overlay')
                            ->label('Прозрачность оверлея')
                            ->default('rgba(0,0,0,0.5)')
                            ->helperText('Цвет и прозрачность темного слоя поверх картинки'),
                        TextInput::make('extra_data.text_color')
                            ->label('Цвет текста')
                            ->default('#ffffff')
                            ->helperText('Цвет текста секции'),
                        Toggle::make('is_active')
                            ->label('Показывать секцию')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(50),
                \Filament\Tables\Columns\ImageColumn::make('image')
                    ->label('Фон')
                    ->disk('public')
                    ->circular(),
                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Активна'),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SectionSettings\Pages\ListSectionSettings::route('/'),
            'edit' => EditSectionSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
