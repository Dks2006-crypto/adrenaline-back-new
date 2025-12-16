<?php

namespace App\Filament\Resources\Attendances;

use App\Filament\Resources\Attendances\Pages\CreateAttendance;
use App\Filament\Resources\Attendances\Pages\EditAttendance;
use App\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Resources\Attendances\Pages\ViewAttendance;
use App\Models\Attendance;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Посещаемость';
    protected static ?string $modelLabel = 'Посещение';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.user.email')
                    ->label('Клиент'),
                TextColumn::make('booking.class.service.title')
                    ->label('Занятие')
                    ->default('Персональная'),
                TextColumn::make('checked_in_at')
                    ->dateTime('d.m.Y H:i')
                    ->label('Заход'),
                TextColumn::make('checked_out_at')
                    ->dateTime('d.m.Y H:i')
                    ->label('Выход'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->filters([
                TernaryFilter::make('checked_in_at')
                    ->label('Зарегистрирован'),
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
            'index' => ListAttendances::route('/'),
            'view' => ViewAttendance::route('/{record}'),
        ];
    }
}
