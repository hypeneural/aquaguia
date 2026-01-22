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
                                    ->description('Digite o nome da cidade para buscar')
                                    ->schema([
                                        Forms\Components\Select::make('city_id')
                                            ->label('Cidade')
                                            ->relationship('city', 'name')
                                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - {$record->state->abbr}")
                                            ->searchable(['name'])
                                            ->required()
                                            ->helperText('Digite pelo menos 3 letras para buscar')
                                            ->columnSpanFull(),
                                    ]),

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
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('Preços Principais')
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
                                        Forms\Components\TextInput::make('price_senior')
                                            ->label('Preço Idoso')
                                            ->numeric()
                                            ->prefix('R$'),
                                        Forms\Components\TextInput::make('price_child_free_under')
                                            ->label('Gratuito até (idade)')
                                            ->numeric()
                                            ->suffix('anos')
                                            ->default(3),
                                        Forms\Components\TextInput::make('price_senior_age_from')
                                            ->label('Idoso a partir de')
                                            ->numeric()
                                            ->suffix('anos')
                                            ->default(60),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('Serviços Extras')
                                    ->schema([
                                        Forms\Components\TextInput::make('price_parking')
                                            ->label('Estacionamento')
                                            ->numeric()
                                            ->prefix('R$')
                                            ->default(0),
                                        Forms\Components\TextInput::make('price_locker')
                                            ->label('Armário Padrão')
                                            ->numeric()
                                            ->prefix('R$')
                                            ->default(0),
                                        Forms\Components\TextInput::make('price_locker_small')
                                            ->label('Armário Pequeno')
                                            ->numeric()
                                            ->prefix('R$'),
                                        Forms\Components\TextInput::make('price_locker_large')
                                            ->label('Armário Grande')
                                            ->numeric()
                                            ->prefix('R$'),
                                        Forms\Components\TextInput::make('price_locker_family')
                                            ->label('Armário Família')
                                            ->numeric()
                                            ->prefix('R$'),
                                        Forms\Components\TextInput::make('price_vip_cabana')
                                            ->label('Cabana VIP')
                                            ->numeric()
                                            ->prefix('R$'),
                                        Forms\Components\TextInput::make('price_all_inclusive')
                                            ->label('All Inclusive')
                                            ->numeric()
                                            ->prefix('R$'),
                                        Forms\Components\DatePicker::make('price_valid_until')
                                            ->label('Preços válidos até'),
                                    ])
                                    ->columns(4),
                            ]),

                        Forms\Components\Tabs\Tab::make('Localização')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Section::make('Endereço')
                                    ->schema([
                                        Forms\Components\TextInput::make('address_street')
                                            ->label('Rua/Avenida')
                                            ->placeholder('Av. Porto das Dunas, 2734')
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('address_neighborhood')
                                            ->label('Bairro')
                                            ->placeholder('Porto das Dunas'),
                                        Forms\Components\TextInput::make('address_zip_code')
                                            ->label('CEP')
                                            ->placeholder('61700-000')
                                            ->mask('99999-999'),
                                    ])
                                    ->columns(4),

                                Forms\Components\Section::make('Coordenadas GPS')
                                    ->description('Importantes para cálculo de distância e integração com mapas')
                                    ->schema([
                                        Forms\Components\TextInput::make('latitude')
                                            ->label('Latitude')
                                            ->numeric()
                                            ->placeholder('-3.8844'),
                                        Forms\Components\TextInput::make('longitude')
                                            ->label('Longitude')
                                            ->numeric()
                                            ->placeholder('-38.3925'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contato')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Section::make('Website e Email')
                                    ->schema([
                                        Forms\Components\TextInput::make('website')
                                            ->label('Website Oficial')
                                            ->url()
                                            ->placeholder('https://www.beachpark.com.br')
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('contact_email')
                                            ->label('Email')
                                            ->email()
                                            ->placeholder('contato@beachpark.com.br'),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('Telefones')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact_phone')
                                            ->label('Telefone')
                                            ->tel()
                                            ->placeholder('+55 85 3361-0000'),
                                        Forms\Components\TextInput::make('contact_whatsapp')
                                            ->label('WhatsApp')
                                            ->placeholder('+5585999999999')
                                            ->helperText('Apenas números com código do país'),
                                        Forms\Components\TextInput::make('contact_whatsapp_message')
                                            ->label('Mensagem Inicial WhatsApp')
                                            ->placeholder('Olá! Quero saber mais sobre o parque')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Redes Sociais')
                                    ->schema([
                                        Forms\Components\TextInput::make('social_instagram')
                                            ->label('Instagram @')
                                            ->placeholder('beachpark')
                                            ->prefix('@'),
                                        Forms\Components\TextInput::make('social_instagram_url')
                                            ->label('URL Instagram')
                                            ->url()
                                            ->placeholder('https://instagram.com/beachpark'),
                                        Forms\Components\TextInput::make('social_facebook_url')
                                            ->label('URL Facebook')
                                            ->url()
                                            ->placeholder('https://facebook.com/beachpark'),
                                        Forms\Components\TextInput::make('social_youtube_url')
                                            ->label('URL YouTube')
                                            ->url()
                                            ->placeholder('https://youtube.com/beachpark'),
                                        Forms\Components\TextInput::make('social_tiktok_url')
                                            ->label('URL TikTok')
                                            ->url()
                                            ->placeholder('https://tiktok.com/@beachpark'),
                                        Forms\Components\TextInput::make('social_twitter_url')
                                            ->label('URL Twitter/X')
                                            ->url()
                                            ->placeholder('https://twitter.com/beachpark'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Reserva / Compra de Ingressos')
                                    ->schema([
                                        Forms\Components\TextInput::make('booking_url')
                                            ->label('URL de Compra')
                                            ->url()
                                            ->placeholder('https://beachpark.com.br/ingressos')
                                            ->columnSpan(2),
                                        Forms\Components\Toggle::make('booking_is_external')
                                            ->label('Link Externo')
                                            ->default(true)
                                            ->helperText('Abre em nova aba'),
                                        Forms\Components\TextInput::make('booking_partner_name')
                                            ->label('Nome do Parceiro')
                                            ->placeholder('Beach Park'),
                                        Forms\Components\TextInput::make('booking_affiliate_code')
                                            ->label('Código de Afiliado')
                                            ->placeholder('aquaguia2026'),
                                    ])
                                    ->columns(3),
                            ]),

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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Parque'),
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
            RelationManagers\OperatingHoursRelationManager::class,
            RelationManagers\SpecialHoursRelationManager::class,
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
