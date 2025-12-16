<?php

namespace App\Filament\Resources\Bookings;

use App\Filament\Resources\Bookings\Pages\CreateBooking;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Models\Booking;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Бронирования';

    public static function getRecordTitle(?Model $record): ?string
    {
        if (!$record) {
            return null;
        }

        if (!$record instanceof Booking) {
            return $record->getKey();
        }

        $userName = $record->user?->name ?? 'Клиент';

        // Определяем тип тренировки
        if ($record->group_class_id) {
            // Групповая тренировка
            $serviceTitle = $record->groupClass?->service?->title ?? 'Групповая тренировка';
            $trainerName = $record->groupClass?->trainer?->name;
            $trainingTitle = $serviceTitle . ($trainerName ? ' с ' . $trainerName : '');
        } elseif ($record->class_id) {
            // Форма тренировки
            $serviceTitle = $record->form?->service?->title ?? 'Групповая тренировка';
            $trainerName = $record->form?->trainer?->name;
            $trainingTitle = $serviceTitle . ($trainerName ? ' с ' . $trainerName : '');
        } else {
            // Персональная тренировка
            $trainerName = $record->trainer?->name ?? 'Тренер';
            $trainingTitle = 'Персональная тренировка с ' . $trainerName;
        }

        return $userName . ' → ' . $trainingTitle;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'email')
                    ->required(),
                Select::make('class_id')
                    ->relationship('class.service', 'title')
                    ->label('Форма тренировки'),
                Select::make('group_class_id')
                    ->relationship('groupClass.service', 'title')
                    ->label('Групповое занятие'),
                Select::make('trainer_id')
                    ->relationship('trainer', 'name')
                    ->label('Тренер'),
                Select::make('status')
                    ->options([
                        \App\Models\Booking::STATUS_PENDING => 'Ожидает',
                        \App\Models\Booking::STATUS_CONFIRMED => 'Подтверждено',
                        \App\Models\Booking::STATUS_CANCELLED => 'Отменено',
                        'completed' => 'Завершено',
                    ])
                    ->label('Статус'),
                Textarea::make('note')
                    ->label('Примечания'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('Пользователь'),
                TextColumn::make('training_type')
                    ->label('Тип')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'group' => 'Групповая',
                            'form' => 'Групповая (форма)',
                            'personal' => 'Персональная',
                            default => 'Неизвестно',
                        };
                    }),
                TextColumn::make('training_description')
                    ->label('Занятие')
                    ->wrap(),
                \Filament\Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => \App\Models\Booking::STATUS_PENDING,
                        'success' => \App\Models\Booking::STATUS_CONFIRMED,
                        'danger' => \App\Models\Booking::STATUS_CANCELLED,
                    ]),
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
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
