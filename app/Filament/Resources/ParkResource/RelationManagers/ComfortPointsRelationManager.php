<?php

namespace App\Filament\Resources\ParkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ComfortPointsRelationManager extends RelationManager
{
    protected static string $relationship = 'comfortPoints';

    protected static ?string $title = 'Pontos de Conforto';

    protected static ?string $modelLabel = 'Ponto de Conforto';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'sombra' => '🌴 Sombra',
                        'fraldario' => '👶 Fraldário',
                        'enfermaria' => '🏥 Enfermaria',
                        'microondas' => '🔥 Microondas',
                        'bebedouro' => '💧 Bebedouro',
                        'alimentacao' => '🍔 Alimentação',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('label')
                    ->label('Nome/Descrição')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('x')
                    ->label('Posição X (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required()
                    ->helperText('Posição horizontal no mapa (0-100)'),
                Forms\Components\TextInput::make('y')
                    ->label('Posição Y (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required()
                    ->helperText('Posição vertical no mapa (0-100)'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'sombra' => '🌴 Sombra',
                        'fraldario' => '👶 Fraldário',
                        'enfermaria' => '🏥 Enfermaria',
                        'microondas' => '🔥 Microondas',
                        'bebedouro' => '💧 Bebedouro',
                        'alimentacao' => '🍔 Alimentação',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('label')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('x')
                    ->label('X')
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('y')
                    ->label('Y')
                    ->suffix('%'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'sombra' => 'Sombra',
                        'fraldario' => 'Fraldário',
                        'enfermaria' => 'Enfermaria',
                        'microondas' => 'Microondas',
                        'bebedouro' => 'Bebedouro',
                        'alimentacao' => 'Alimentação',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
