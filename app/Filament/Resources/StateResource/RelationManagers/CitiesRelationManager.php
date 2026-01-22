<?php

namespace App\Filament\Resources\StateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'cities';

    protected static ?string $title = 'Cidades';

    protected static ?string $modelLabel = 'Cidade';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->helperText('Deixe vazio para gerar automaticamente'),

                Forms\Components\FileUpload::make('image')
                    ->label('Imagem da Cidade')
                    ->image()
                    ->directory('cities')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('450')
                    ->helperText('Imagem 16:9 (800x450px recomendado)'),

                Forms\Components\Toggle::make('featured')
                    ->label('Destaque')
                    ->helperText('Cidades em destaque aparecem na home')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray')
                    ->copyable(),

                Tables\Columns\IconColumn::make('featured')
                    ->label('Destaque')
                    ->boolean(),

                Tables\Columns\TextColumn::make('parks_count')
                    ->label('Parques')
                    ->counts('parks')
                    ->sortable()
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('featured')
                    ->label('Destaque'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Cidade'),
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
