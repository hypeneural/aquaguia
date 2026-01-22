<?php

namespace App\Filament\Resources\ParkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SpecialHoursRelationManager extends RelationManager
{
    protected static string $relationship = 'specialHours';

    protected static ?string $title = 'Horários Especiais';

    protected static ?string $modelLabel = 'Período Especial';

    protected static ?string $pluralModelLabel = 'Períodos Especiais';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome do Período')
                    ->placeholder('Temporada de Verão, Feriado de Natal...')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Data Início')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Data Fim')
                    ->required()
                    ->native(false)
                    ->afterOrEqual('start_date'),

                Forms\Components\Toggle::make('is_closed')
                    ->label('Fechado neste período')
                    ->reactive()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('closure_reason')
                    ->label('Motivo do Fechamento')
                    ->placeholder('Manutenção programada...')
                    ->maxLength(200)
                    ->visible(fn(Forms\Get $get) => $get('is_closed'))
                    ->columnSpanFull(),

                Forms\Components\TimePicker::make('open_time')
                    ->label('Abertura')
                    ->seconds(false)
                    ->hidden(fn(Forms\Get $get) => $get('is_closed')),

                Forms\Components\TimePicker::make('close_time')
                    ->label('Fechamento')
                    ->seconds(false)
                    ->hidden(fn(Forms\Get $get) => $get('is_closed'))
                    ->after('open_time'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Período')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fim')
                    ->date('d/m/Y'),

                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Fechado')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('open_time')
                    ->label('Horário')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->is_closed
                        ? 'Fechado'
                        : ($record->open_time?->format('H:i') . ' - ' . $record->close_time?->format('H:i'))
                    ),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Período'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
