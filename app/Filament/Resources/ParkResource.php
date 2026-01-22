<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParkResource\Pages;
use App\Filament\Resources\ParkResource\RelationManagers;
use App\Models\Park;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ParkResource extends Resource
{
    protected static ?string $model = Park::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Conteúdo';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Parque';

    protected static ?string $pluralModelLabel = 'Parques';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Parque')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Geral')
                            ->schema([
                                Forms\Components\Select::make('city_id')
                                    ->label('Cidade')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Deixe vazio para gerar automaticamente'),
                                Forms\Components\RichEditor::make('description')
                                    ->label('Descrição')
                                    ->required()
                                    ->columnSpanFull(),
                                SpatieMediaLibraryFileUpload::make('hero')
                                    ->label('Imagem Principal')
                                    ->collection('hero')
                                    ->responsiveImages()
                                    ->required(),
                                Forms\Components\TextInput::make('opening_hours')
                                    ->label('Horário de Funcionamento')
                                    ->placeholder('10h às 17h')
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Preços')
                            ->schema([
                                Forms\Components\TextInput::make('price_adult')
                                    ->label('Preço Adulto')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required(),
                                Forms\Components\TextInput::make('price_child')
                                    ->label('Preço Criança')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required(),
                                Forms\Components\TextInput::make('price_parking')
                                    ->label('Estacionamento')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->default(0),
                                Forms\Components\TextInput::make('price_locker')
                                    ->label('Armário')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->default(0),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Localização')
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->placeholder('-23.5505199'),
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->placeholder('-46.6333094'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Família')
                            ->schema([
                                Forms\Components\TextInput::make('water_heated_areas')
                                    ->label('Áreas Aquecidas')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Número de piscinas/áreas aquecidas'),
                                Forms\Components\Select::make('shade_level')
                                    ->label('Nível de Sombra')
                                    ->options([
                                        'baixa' => 'Baixa',
                                        'média' => 'Média',
                                        'alta' => 'Alta',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('family_index')
                                    ->label('Índice Família')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0)
                                    ->helperText('Pontuação de 0 a 100'),
                                Forms\Components\Select::make('tags')
                                    ->label('Tags')
                                    ->relationship('tags', 'label')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Conteúdo')
                            ->schema([
                                Forms\Components\TagsInput::make('best_for')
                                    ->label('Ideal Para')
                                    ->placeholder('Adicionar item...')
                                    ->helperText('Ex: Famílias com crianças, Grupos grandes'),
                                Forms\Components\TagsInput::make('not_for')
                                    ->label('Não Recomendado Para')
                                    ->placeholder('Adicionar item...')
                                    ->helperText('Ex: Orçamento apertado'),
                                Forms\Components\TagsInput::make('anti_queue_tips')
                                    ->label('Dicas Anti-Fila')
                                    ->placeholder('Adicionar dica...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Ativo')
                                    ->default(true)
                                    ->helperText('Desmarque para ocultar o parque da listagem'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('hero_image')
                    ->label('Imagem')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Cidade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_adult')
                    ->label('Preço Adulto')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('family_index')
                    ->label('Índice Família')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->relationship('city', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttractionsRelationManager::class,
            RelationManagers\PhotosRelationManager::class,
            RelationManagers\VideosRelationManager::class,
            RelationManagers\FaqRelationManager::class,
            RelationManagers\ComfortPointsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParks::route('/'),
            'create' => Pages\CreatePark::route('/create'),
            'edit' => Pages\EditPark::route('/{record}/edit'),
        ];
    }
}
