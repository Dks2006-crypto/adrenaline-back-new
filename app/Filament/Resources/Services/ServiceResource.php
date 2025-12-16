<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Тарифы';

    protected static ?string $modelLabel = 'тариф';

    protected static ?string $pluralModelLabel = 'Тарифы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Название')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->columnSpanFull(),

                Repeater::make('base_benefits')
                    ->label('Базовые преимущества')
                    ->schema([
                        TextInput::make('benefit')
                            ->label('Преимущество')
                            ->required(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['benefit'] ?? null)
                    ->defaultItems(3)
                    ->reorderable()
                    ->addActionLabel('Добавить преимущество'),

                TextInput::make('duration_days')
                    ->label('Длительность (дни)')
                    ->numeric()
                    ->required(),

                TextInput::make('visits_limit')
                    ->label('Лимит посещений')
                    ->numeric()
                    ->nullable(),

                TextInput::make('price_cents')
                    ->label('Цена (в копейках)')
                    ->numeric()
                    ->required()
                    ->helperText('Пример: 490000 = 4900.00 ₽'),

                Toggle::make('active')
                    ->label('Активен')
                    ->default(true),

                Select::make('type')
                    ->label('Тип тарифа')
                    ->options([
                        'single' => 'Разовый',
                        'monthly' => 'Месячный',
                        'yearly' => 'Годовой',
                    ])
                    ->default('monthly')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('price_cents')
                    ->label('Цена')
                    ->money('RUB', divideBy: 100)
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('base_benefits')
                    ->label('Преимущества')
                    ->formatStateUsing(fn ($state): string => is_array($state)
                        ? collect($state)->pluck('benefit')->implode(' • ')
                        : ''
                    )
                    ->limit(60)
                    ->tooltip(fn ($state) => is_array($state) ? collect($state)->pluck('benefit')->implode("\n") : null),

                \Filament\Tables\Columns\BadgeColumn::make('type')
                    ->label('Тип')
                    ->colors([
                        'success' => 'monthly',
                        'warning' => 'single',
                        'info' => 'yearly',
                    ]),

                \Filament\Tables\Columns\ToggleColumn::make('active')
                    ->label('Активен'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                ]),
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
