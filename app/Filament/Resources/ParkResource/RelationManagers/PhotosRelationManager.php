<?php

namespace App\Filament\Resources\ParkResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Galeria de Fotos';

    protected static ?string $modelLabel = 'Foto';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Upload de Foto')
                    ->schema([
                        Forms\Components\FileUpload::make('url')
                            ->label('Imagem')
                            ->image()
                            ->imageEditor()
                            ->directory('parks/photos')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('675')
                            ->helperText('Arraste a imagem ou clique para fazer upload. Formato 16:9 recomendado.')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('caption')
                            ->label('Legenda')
                            ->placeholder('Descreva brevemente a imagem...')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('display_order')
                            ->default(fn() => 0),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('display_order')
            ->defaultSort('display_order')
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('url')
                        ->label('Foto')
                        ->height(150)
                        ->width(266)
                        ->extraImgAttributes(['class' => 'rounded-lg']),

                    Tables\Columns\TextColumn::make('caption')
                        ->label('Legenda')
                        ->limit(40)
                        ->color('gray')
                        ->size('sm'),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Foto')
                    ->modalHeading('Adicionar Nova Foto'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar Foto'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhuma foto cadastrada')
            ->emptyStateDescription('Adicione fotos para a galeria do parque.')
            ->emptyStateIcon('heroicon-o-photo');
    }
}
