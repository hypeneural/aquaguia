<?php

namespace App\Filament\Resources\ParkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OperatingHoursRelationManager extends RelationManager
{
    protected static string $relationship = 'operatingHours';

    protected static ?string $title = 'Horários de Funcionamento';

    protected static ?string $modelLabel = 'Horário';

    protected static ?string $pluralModelLabel = 'Horários';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day_of_week')
                    ->label('Dia da Semana')
                    ->options([
                        'monday' => 'Segunda-feira',
                        'tuesday' => 'Terça-feira',
                        'wednesday' => 'Quarta-feira',
                        'thursday' => 'Quinta-feira',
                        'friday' => 'Sexta-feira',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\Toggle::make('is_closed')
                    ->label('Fechado')
                    ->reactive()
                    ->helperText('Marque se o parque não abre neste dia'),

                Forms\Components\TimePicker::make('open_time')
                    ->label('Abertura')
                    ->seconds(false)
                    ->required(fn(Forms\Get $get) => !$get('is_closed'))
                    ->disabled(fn(Forms\Get $get) => $get('is_closed')),

                Forms\Components\TimePicker::make('close_time')
                    ->label('Fechamento')
                    ->seconds(false)
                    ->required(fn(Forms\Get $get) => !$get('is_closed'))
                    ->disabled(fn(Forms\Get $get) => $get('is_closed'))
                    ->after('open_time'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Dia')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'monday' => 'Segunda-feira',
                        'tuesday' => 'Terça-feira',
                        'wednesday' => 'Quarta-feira',
                        'thursday' => 'Quinta-feira',
                        'friday' => 'Sexta-feira',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Fechado')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('open_time')
                    ->label('Abertura')
                    ->time('H:i')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('close_time')
                    ->label('Fechamento')
                    ->time('H:i')
                    ->placeholder('—'),
            ])
            ->defaultSort('day_of_week')
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Horário'),
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
