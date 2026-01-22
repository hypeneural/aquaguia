<?php

namespace App\Filament\Resources\ParkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Fotos';

    protected static ?string $modelLabel = 'Foto';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('url')
                    ->label('Imagem')
                    ->image()
                    ->directory('parks/photos')
                    ->required(),
                Forms\Components\TextInput::make('caption')
                    ->label('Legenda')
                    ->maxLength(255),
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
                Tables\Columns\ImageColumn::make('url')
                    ->label('Foto')
                    ->height(80),
                Tables\Columns\TextColumn::make('caption')
                    ->label('Legenda')
                    ->limit(50),
                Tables\Columns\TextColumn::make('display_order')
                    ->label('Ordem')
                    ->sortable(),
            ])
            ->filters([
                //
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
