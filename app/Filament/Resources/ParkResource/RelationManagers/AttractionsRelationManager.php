<?php

namespace App\Filament\Resources\ParkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'attractions';

    protected static ?string $title = 'Atrações';

    protected static ?string $modelLabel = 'Atração';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'infantil' => 'Infantil',
                        'família' => 'Família',
                        'radical' => 'Radical',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('min_height_cm')
                    ->label('Altura Mínima (cm)')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('max_height_cm')
                    ->label('Altura Máxima (cm)')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('adrenaline')
                    ->label('Adrenalina (0-5)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(5)
                    ->default(0),
                Forms\Components\TextInput::make('avg_queue_minutes')
                    ->label('Tempo Médio de Fila (min)')
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('has_double_float')
                    ->label('Boia Dupla'),
                Forms\Components\FileUpload::make('image')
                    ->label('Imagem')
                    ->image()
                    ->directory('attractions'),
                Forms\Components\Toggle::make('is_open')
                    ->label('Em Funcionamento')
                    ->default(true),
                Forms\Components\TextInput::make('display_order')
                    ->label('Ordem')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('display_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'infantil' => 'success',
                        'família' => 'info',
                        'radical' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('min_height_cm')
                    ->label('Altura Mín.')
                    ->suffix(' cm'),
                Tables\Columns\TextColumn::make('adrenaline')
                    ->label('Adrenalina'),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Aberto')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'infantil' => 'Infantil',
                        'família' => 'Família',
                        'radical' => 'Radical',
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
