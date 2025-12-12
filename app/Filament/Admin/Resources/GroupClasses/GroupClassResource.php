<?php

namespace App\Filament\Admin\Resources\GroupClasses;

use App\Filament\Admin\Resources\GroupClasses\Pages;
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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class GroupClassResource extends Resource
{
    protected static ?string $model = GroupClass::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Групповые занятия';
    protected static ?string $pluralLabel = 'Групповые занятия';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Основная информация')
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

                Section::make('Дата и время')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Дата занятия')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y')
                            ->firstDayOfWeek(1)
                            ->closeOnDateSelection()
                            ->reactive(),

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

                        // Автоматически собираем дату+время в нужные поля
                        Hidden::make('starts_at')
                            ->dehydrated(true)
                            ->afterStateHydrated(
                                fn($state, $set, $record) =>
                                $record?->starts_at && $set('time_start', $record->starts_at->format('H:i'))
                            )
                            ->mutateDehydratedStateUsing(
                                fn($state, $get) =>
                                $get('date') && $get('time_start')
                                    ? \Carbon\Carbon::createFromFormat('Y-m-d H:i', $get('date') . ' ' . $get('time_start'))
                                    : null
                            ),

                        Hidden::make('ends_at')
                            ->dehydrated(true)
                            ->afterStateHydrated(
                                fn($state, $set, $record) =>
                                $record?->ends_at && $set('time_end', $record->ends_at->format('H:i'))
                            )
                            ->mutateDehydratedStateUsing(
                                fn($state, $get) =>
                                $get('date') && $get('time_end')
                                    ? \Carbon\Carbon::createFromFormat('Y-m-d H:i', $get('date') . ' ' . $get('time_end'))
                                    : null
                            ),
                    ])
                    ->columns(3),

                Section::make('Повторение занятия')
                    ->schema([
                        ToggleButtons::make('recurrence_type')
                            ->label('Тип повторения')
                            ->options([
                                'none' => 'Без повторения',
                                'daily' => 'Ежедневно',
                                'weekly' => 'Еженедельно',
                                'custom_weekly' => 'По выбранным дням недели',
                                'monthly' => 'Ежемесячно',
                            ])
                            ->default('none')
                            ->inline()
                            ->reactive(),

                        Grid::make(1)
                            ->schema([
                                CheckboxList::make('weekly_days')
                                    ->label('Дни недели')
                                    ->options([
                                        'MO' => 'Понедельник',
                                        'TU' => 'Вторник',
                                        'WE' => 'Среда',
                                        'TH' => 'Четверг',
                                        'FR' => 'Пятница',
                                        'SA' => 'Суббота',
                                        'SU' => 'Воскресенье',
                                    ])
                                    ->columns(4)
                                    ->visible(fn($get) => $get('recurrence_type') === 'custom_weekly'),

                                TextInput::make('repeat_count')
                                    ->label('Количество повторений')
                                    ->numeric()
                                    ->default(4)
                                    ->helperText('Сколько занятий создать (включая первое)')
                                    ->visible(fn($get) => in_array($get('recurrence_type'), ['daily', 'weekly', 'custom_weekly', 'monthly'])),
                            ])
                            ->visible(fn($get) => $get('recurrence_type') !== 'none'),

                        Placeholder::make('preview')
                            ->label('Предпросмотр дат')
                            ->content(function ($get) {
                                $type = $get('recurrence_type');
                                if ($type === 'none' || !$get('starts_at')) {
                                    return '—';
                                }

                                $start = Carbon::parse($get('starts_at'));
                                $count = (int) ($get('repeat_count') ?? 1);
                                $days = $get('weekly_days') ?? [];

                                $dates = [];

                                for ($i = 0; $i < $count; $i++) {
                                    $current = match ($type) {
                                        'daily'         => $start->copy()->addDays($i),
                                        'weekly'        => $start->copy()->addWeeks($i),
                                        'custom_weekly' => $start->copy()->addWeeks($i),
                                        'monthly'       => $start->copy()->addMonthsNoOverflow($i),
                                        default         => $start,
                                    };

                                    if ($type === 'custom_weekly') {
                                        $dayAbbr = $current->format('D'); // Mon, Tue...
                                        $map = ['Mon' => 'MO', 'Tue' => 'TU', 'Wed' => 'WE', 'Thu' => 'TH', 'Fri' => 'FR', 'Sat' => 'SA', 'Sun' => 'SU'];
                                        if (!in_array($map[$dayAbbr], $days)) {
                                            continue;
                                        }
                                    }

                                    $dates[] = $current->translatedFormat('d.m.Y (D)');
                                }

                                return $dates
                                    ? 'Будет создано занятий: ' . implode(', ', $dates)
                                    : 'Выберите хотя бы один день недели';
                            })
                            ->visible(fn($get) => in_array($get('recurrence_type'), ['daily', 'weekly', 'custom_weekly', 'monthly'])),
                    ]),

                Section::make('Бронирование и цена')
                    ->schema([
                        TextInput::make('capacity')
                            ->label('Вместимость')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(999)
                            ->required()
                            ->default(10),

                        TextInput::make('price_rub')
                            ->label('Цена за занятие')
                            ->numeric()
                            ->suffix(' ₽')
                            ->minValue(0)
                            ->required()
                            ->default(800)
                            ->reactive()
                            ->afterStateUpdated(fn($state, $set) => $set('price_cents', (int)($state * 100))),

                        Hidden::make('price_cents')
                            ->afterStateHydrated(
                                fn($state, $set, $get) =>
                                $set('price_rub', $state ? $state / 100 : null)
                            )
                            ->dehydrated(fn($state) => filled($state)),

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
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service.title')
                    ->label('Услуга')
                    ->badge(),

                TextColumn::make('trainer.name')
                    ->label('Тренер')
                    ->placeholder('—'),

                TextColumn::make('starts_at')
                    ->label('Дата и время')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('capacity')
                    ->label('Мест'),

                TextColumn::make('price_cents')
                    ->money('RUB', 100),

                ToggleColumn::make('active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
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
