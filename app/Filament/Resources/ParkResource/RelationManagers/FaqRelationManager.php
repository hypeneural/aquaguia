<?php

namespace App\Filament\Resources\ParkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FaqRelationManager extends RelationManager
{
    protected static string $relationship = 'faq';

    protected static ?string $title = 'Perguntas Frequentes';

    protected static ?string $modelLabel = 'FAQ';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->label('Pergunta')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('answer')
                    ->label('Resposta')
                    ->required()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('question')
                    ->label('Pergunta')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('answer')
                    ->label('Resposta')
                    ->html()
                    ->limit(80),
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
