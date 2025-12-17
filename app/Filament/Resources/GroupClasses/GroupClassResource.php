<?php

namespace App\Filament\Resources\GroupClasses;

use App\Filament\Resources\GroupClasses\Pages;
use App\Models\GroupClass;
use App\Models\Service;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class GroupClassResource extends Resource
{
    protected static ?string $model = GroupClass::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Групповые занятия';
    protected static ?string $pluralLabel = 'Групповые занятия';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Основная информация')
                    ->schema([
                        TextInput::make('title')
                            ->label('Название занятия')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(3)
                            ->nullable(),

                        Hidden::make('service_id')
                            ->default(1)
                            ->dehydrated(true),

                        Select::make('trainer_id')
                            ->label('Тренер')
                            ->options(User::where('role_id', 2)->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columns(2),

                \Filament\Forms\Components\Section::make('Дата и время')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Дата занятия')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->firstDayOfWeek(1)
                            ->closeOnDateSelection()
                            ->reactive()
                            ->dehydrateStateUsing(fn ($state) => Carbon::parse($state)->toDateString()),

                        TimePicker::make('time_start')
                            ->label('Начало')
                            ->required()
                            ->native(false)
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->minutesStep(5)
                            ->reactive(),

                        TimePicker::make('time_end')
                            ->label('Окончание')
                            ->required()
                            ->native(false)
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->minutesStep(5)
                            ->after('time_start')
                            ->reactive(),

                        // Автоматически собираем дату+время в нужные поля STARTS_AT
                        Hidden::make('starts_at')
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($state, $set, $record) {
                                if ($record?->starts_at) {
                                    $set('time_start', $record->starts_at->format('H:i'));
                                    $set('date', $record->starts_at->toDateString());
                                }
                            })
                            ->mutateDehydratedStateUsing(
                                function ($state, $get) {
                                    $date = $get('date');
                                    $time_start = $get('time_start');
                        
                                    if (!$date || !$time_start) {
                                        return null;
                                    }
                        
                                    // *** ИСПРАВЛЕНИЕ #1: Гарантируем чистую дату Y-m-d ***
                                    $parsed_date = \Carbon\Carbon::parse($date)->toDateString();
                                    
                                    // *** ИСПРАВЛЕНИЕ #2: Гарантируем чистое время H:i ***
                                    $parsed_time_start = \Carbon\Carbon::parse($time_start)->format('H:i');
                        
                                    $combined_string = $parsed_date . ' ' . $parsed_time_start;
                        
                                    return \Carbon\Carbon::createFromFormat(
                                        'Y-m-d H:i', 
                                        $combined_string
                                    );
                                }
                            ),
                        
                        // Автоматически собираем дату+время в нужные поля ENDS_AT
                        Hidden::make('ends_at')
                            ->dehydrated(true)
                            ->afterStateHydrated(function ($state, $set, $record) {
                                if ($record?->ends_at) {
                                    $set('time_end', $record->ends_at->format('H:i'));
                                }
                            })
                            ->mutateDehydratedStateUsing(
                                function ($state, $get) {
                                    $date = $get('date');
                                    $time_end = $get('time_end');
                        
                                    if (!$date || !$time_end) {
                                        return null;
                                    }
                        
                                    // *** ИСПРАВЛЕНИЕ #1: Гарантируем чистую дату Y-m-d ***
                                    $parsed_date = \Carbon\Carbon::parse($date)->toDateString();
                                    
                                    // *** ИСПРАВЛЕНИЕ #2: Гарантируем чистое время H:i ***
                                    $parsed_time_end = \Carbon\Carbon::parse($time_end)->format('H:i'); // Используем time_end
                        
                                    $combined_string = $parsed_date . ' ' . $parsed_time_end;
                        
                                    return \Carbon\Carbon::createFromFormat(
                                        'Y-m-d H:i', 
                                        $combined_string
                                    );
                                }
                            ),
                    ])
                    ->columns(3),

                \Filament\Forms\Components\Section::make('Бронирование и цена')
                    ->schema([
                        TextInput::make('capacity')
                            ->label('Вместимость')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(999)
                            ->required()
                            ->default(10),


                        Hidden::make('currency')
                            ->default('RUB'),

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
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('service.title')
                    ->label('Услуга')
                    ->badge(),

                \Filament\Tables\Columns\TextColumn::make('trainer.name')
                    ->label('Тренер')
                    ->placeholder('—'),

                \Filament\Tables\Columns\TextColumn::make('starts_at')
                    ->label('Дата и время')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('capacity')
                    ->label('Мест'),


                \Filament\Tables\Columns\ToggleColumn::make('active'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListGroupClasses::route('/'),
            'create' => Pages\CreateGroupClass::route('/create'),
            'edit'   => Pages\EditGroupClass::route('/{record}/edit'),
        ];
    }
}