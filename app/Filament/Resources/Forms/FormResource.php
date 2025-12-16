<?php

namespace App\Filament\Resources\Forms;

use App\Filament\Resources\Forms\Pages\CreateForm;
use App\Filament\Resources\Forms\Pages\EditForm;
use App\Filament\Resources\Forms\Pages\ListForms;
use App\Models\Form as FormModel;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FormResource extends Resource
{
    protected static ?string $model = FormModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Занятия';
    protected static ?string $modelLabel = 'Занятие';

    public static function getRecordTitle(?Model $record): ?string
{
    if (!$record) {
        return null;
    }

    return match (true) {

        $record instanceof FormModel =>
            $record->service?->title . ' — ' . $record->starts_at?->format('d.m.Y H:i'),


        default => $record->getKey(),
    };
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('service_id')
                    ->relationship('service', 'title')
                    ->required(),
                Select::make('trainer_id')
                    ->relationship('trainer.user', 'name'),
                DateTimePicker::make('starts_at')->required(),
                DateTimePicker::make('ends_at')->required(),
                TextInput::make('capacity')->numeric()->required(),
                TextInput::make('recurrence_rule'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.title'),
                TextColumn::make('trainer.user.name'),
                TextColumn::make('starts_at')
                    ->dateTime('d.m H:i'),
                TextColumn::make('capacity'),
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
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }
}
