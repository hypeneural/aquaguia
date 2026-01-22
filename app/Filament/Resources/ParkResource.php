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
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Localização')
                                    ->description('Selecione o estado e a cidade do parque')
                                    ->schema([
                                        Forms\Components\Select::make('state_id')
                                            ->label('Estado')
                                            ->options(fn() => \App\Models\State::orderBy('name')->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(fn(Forms\Set $set) => $set('city_id', null))
                                            ->dehydrated(false)
                                            ->required(),
                                        Forms\Components\Select::make('city_id')
                                            ->label('Cidade')
                                            ->options(
                                                fn(Forms\Get $get) =>
                                                \App\Models\City::where('state_id', $get('state_id'))
                                                    ->orderBy('name')
                                                    ->pluck('name', 'id')
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->disabled(fn(Forms\Get $get) => !$get('state_id'))
                                            ->helperText('Primeiro selecione o estado'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Informações Básicas')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome do Parque')
                                            ->required()
                                            ->maxLength(100)
                                            ->placeholder('Ex: Beach Park'),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug (URL)')
                                            ->maxLength(100)
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Deixe vazio para gerar automaticamente')
                                            ->placeholder('beach-park'),
                                        Forms\Components\TextInput::make('opening_hours')
                                            ->label('Horário de Funcionamento')
                                            ->placeholder('Qui-Dom: 10h às 17h')
                                            ->required()
                                            ->maxLength(50)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Imagem e Descrição')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('hero')
                                            ->label('Imagem Principal (Hero)')
                                            ->collection('hero')
                                            ->responsiveImages()
                                            ->helperText('Imagem que aparece no topo. Tamanho recomendado: 1920x1080')
                                            ->required(),
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Descrição do Parque')
                                            ->placeholder('Descreva o parque, suas principais atrações e diferenciais...')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

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
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\Section::make('Avaliação Familiar')
                                    ->description('Configure as características para famílias')
                                    ->schema([
                                        Forms\Components\Radio::make('family_index')
                                            ->label('Índice Família')
                                            ->inline()
                                            ->options([
                                                1 => '1 ⚡ Radical',
                                                2 => '2',
                                                3 => '3 ⚖️ Equilibrado',
                                                4 => '4',
                                                5 => '5 👨‍👩‍👧‍👦 Familiar',
                                            ])
                                            ->default(3)
                                            ->helperText('1 = Mais radical, 5 = Mais familiar')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('water_heated_areas')
                                            ->label('Áreas com Água Aquecida')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('áreas')
                                            ->helperText('Quantidade de piscinas/áreas com água quente'),
                                        Forms\Components\ToggleButtons::make('shade_level')
                                            ->label('Nível de Sombra')
                                            ->inline()
                                            ->options([
                                                'baixa' => '☀️ Baixa',
                                                'média' => '⛅ Média',
                                                'alta' => '🌴 Alta',
                                            ])
                                            ->required()
                                            ->helperText('Quantidade de áreas sombreadas'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Categorias')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('tags')
                                            ->label('Tags do Parque')
                                            ->relationship('tags', 'label')
                                            ->columns(3)
                                            ->helperText('Selecione todas as tags aplicáveis'),
                                    ]),
                            ]),

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
