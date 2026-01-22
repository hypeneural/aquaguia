<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\City;
use App\Models\ComfortPoint;
use App\Models\Park;
use App\Models\ParkFaq;
use App\Models\ParkPhoto;
use App\Models\ParkVideo;
use App\Models\State;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ParkDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Criando cidades...');
        $this->createCities();

        $this->command->info('Criando parques...');
        $this->createParks();

        $this->command->info('Cidades e Parques criados com sucesso!');
    }

    private function createCities(): void
    {
        $cities = [
            // Ceará
            ['state' => 'CE', 'name' => 'Aquiraz', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/aquiraz.jpg'],
            ['state' => 'CE', 'name' => 'Fortaleza', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/fortaleza.jpg'],

            // São Paulo
            ['state' => 'SP', 'name' => 'Olímpia', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/olimpia.jpg'],
            ['state' => 'SP', 'name' => 'Socorro', 'featured' => false, 'image' => 'https://cdn.aquaguia.com/cities/socorro.jpg'],
            ['state' => 'SP', 'name' => 'Campinas', 'featured' => false, 'image' => 'https://cdn.aquaguia.com/cities/campinas.jpg'],

            // Goiás
            ['state' => 'GO', 'name' => 'Caldas Novas', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/caldas-novas.jpg'],
            ['state' => 'GO', 'name' => 'Rio Quente', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/rio-quente.jpg'],

            // Santa Catarina
            ['state' => 'SC', 'name' => 'Penha', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/penha.jpg'],
            ['state' => 'SC', 'name' => 'Florianópolis', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/florianopolis.jpg'],
            ['state' => 'SC', 'name' => 'Balneário Camboriú', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/balneario-camboriu.jpg'],

            // Rio Grande do Sul
            ['state' => 'RS', 'name' => 'Capão da Canoa', 'featured' => false, 'image' => 'https://cdn.aquaguia.com/cities/capao-canoa.jpg'],

            // Rio de Janeiro
            ['state' => 'RJ', 'name' => 'Rio de Janeiro', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/rio-de-janeiro.jpg'],

            // Bahia
            ['state' => 'BA', 'name' => 'Salvador', 'featured' => true, 'image' => 'https://cdn.aquaguia.com/cities/salvador.jpg'],

            // Pernambuco
            ['state' => 'PE', 'name' => 'Ipojuca', 'featured' => false, 'image' => 'https://cdn.aquaguia.com/cities/ipojuca.jpg'],
        ];

        foreach ($cities as $cityData) {
            $state = State::where('abbr', $cityData['state'])->first();
            if ($state) {
                City::firstOrCreate(
                    ['name' => $cityData['name'], 'state_id' => $state->id],
                    [
                        'slug' => Str::slug($cityData['name']),
                        'image' => $cityData['image'],
                        'featured' => $cityData['featured'],
                    ]
                );
            }
        }
    }

    private function createParks(): void
    {
        $parks = [
            // Beach Park - Ceará
            [
                'city' => 'Aquiraz',
                'name' => 'Beach Park',
                'description' => 'O Beach Park é o maior parque aquático da América Latina, localizado na Praia de Porto das Dunas, em Aquiraz, no Ceará. Com mais de 18 atrações radicais e um complexo de resort à beira-mar, oferece diversão para toda a família.',
                'hero_image' => 'https://cdn.aquaguia.com/parks/beach-park/hero.jpg',
                'latitude' => -3.8844,
                'longitude' => -38.3925,
                'opening_hours' => 'Qui-Dom: 10h às 17h',
                'price_adult' => 340.00,
                'price_child' => 255.00,
                'price_parking' => 50.00,
                'price_locker' => 35.00,
                'water_heated_areas' => 0,
                'shade_level' => 'média',
                'family_index' => 4,
                'best_for' => ['Famílias com crianças maiores', 'Grupos de amigos', 'Casais aventureiros'],
                'not_for' => ['Quem não gosta de sol intenso', 'Pessoas com medo de altura'],
                'anti_queue_tips' => ['Chegue antes das 10h', 'Evite feriados prolongados', 'Comece pelas atrações mais distantes da entrada'],
                'tags' => ['radical', 'estacionamento'],
                'attractions' => [
                    ['name' => 'Insano', 'type' => 'radical', 'min_height_cm' => 140, 'adrenaline' => 5, 'avg_queue_minutes' => 45, 'description' => 'O toboágua mais alto do Brasil com 41 metros de altura. Velocidade de até 105 km/h.'],
                    ['name' => 'Vaikuntudo', 'type' => 'radical', 'min_height_cm' => 120, 'adrenaline' => 4, 'avg_queue_minutes' => 35, 'description' => 'Toboágua com mega looping e descida em queda livre.'],
                    ['name' => 'Atlantis', 'type' => 'família', 'min_height_cm' => 100, 'adrenaline' => 3, 'avg_queue_minutes' => 25, 'description' => 'Toboágua temático com múltiplas descidas e efeitos especiais.'],
                    ['name' => 'Acqua Show', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 10, 'description' => 'Piscina de ondas com shows de música ao vivo.'],
                    ['name' => 'Arca de Noé', 'type' => 'infantil', 'min_height_cm' => 0, 'max_height_cm' => 140, 'adrenaline' => 1, 'avg_queue_minutes' => 5, 'description' => 'Área infantil com mini toboáguas e playground aquático.', 'has_double_float' => false],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/beach-park/insano.jpg', 'caption' => 'Insano - O toboágua mais alto do Brasil'],
                    ['url' => 'https://cdn.aquaguia.com/parks/beach-park/vista-aerea.jpg', 'caption' => 'Vista aérea do complexo'],
                    ['url' => 'https://cdn.aquaguia.com/parks/beach-park/piscina-ondas.jpg', 'caption' => 'Piscina de ondas'],
                ],
                'videos' => [
                    ['youtube_id' => 'dQw4w9WgXcQ', 'title' => 'Tour completo pelo Beach Park'],
                ],
                'faq' => [
                    ['question' => 'Posso levar comida de fora?', 'answer' => 'Não é permitido entrar com alimentos ou bebidas de fora do parque.'],
                    ['question' => 'O estacionamento é gratuito?', 'answer' => 'O estacionamento é pago, com valor de R$ 50,00 por veículo.'],
                    ['question' => 'Tem área para bebês?', 'answer' => 'Sim! A Arca de Noé é perfeita para os pequenos com até 1,40m.'],
                ],
                'comfort_points' => [
                    ['type' => 'fraldario', 'label' => 'Fraldário Principal', 'x' => 0.35, 'y' => 0.42],
                    ['type' => 'enfermaria', 'label' => 'Enfermaria', 'x' => 0.50, 'y' => 0.30],
                    ['type' => 'alimentacao', 'label' => 'Praça de Alimentação', 'x' => 0.60, 'y' => 0.55],
                ],
            ],

            // Hot Beach - São Paulo
            [
                'city' => 'Olímpia',
                'name' => 'Hot Beach',
                'description' => 'O Hot Beach é um dos parques mais modernos de Olímpia, com águas naturalmente quentes a 37°C. Combina atrações radicais com áreas de relaxamento e spa.',
                'hero_image' => 'https://cdn.aquaguia.com/parks/hot-beach/hero.jpg',
                'latitude' => -20.7433,
                'longitude' => -48.9125,
                'opening_hours' => 'Ter-Dom: 09h às 17h',
                'price_adult' => 180.00,
                'price_child' => 120.00,
                'price_parking' => 30.00,
                'price_locker' => 25.00,
                'water_heated_areas' => 8,
                'shade_level' => 'alta',
                'family_index' => 5,
                'best_for' => ['Famílias com crianças pequenas', 'Idosos', 'Quem busca relaxamento'],
                'not_for' => ['Quem busca atrações extremamente radicais'],
                'anti_queue_tips' => ['Vá durante a semana', 'O período da tarde é mais tranquilo'],
                'tags' => ['aquecida', 'bebes-ok', 'sombra-boa', 'estacionamento'],
                'attractions' => [
                    ['name' => 'Mega Tobogã', 'type' => 'radical', 'min_height_cm' => 120, 'adrenaline' => 4, 'avg_queue_minutes' => 20, 'description' => 'Toboágua com 70m de extensão e 3 descidas diferentes.'],
                    ['name' => 'Rio Lento', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Rio artificial para relaxar em boias.', 'has_double_float' => true],
                    ['name' => 'Praia das Crianças', 'type' => 'infantil', 'min_height_cm' => 0, 'max_height_cm' => 120, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Praia artificial com água quentinha para os pequenos.'],
                    ['name' => 'Piscina de Ondas', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 2, 'avg_queue_minutes' => 5, 'description' => 'Piscina com ondas artificiais aquecidas.'],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/hot-beach/piscinas.jpg', 'caption' => 'Piscinas aquecidas'],
                    ['url' => 'https://cdn.aquaguia.com/parks/hot-beach/toboaguas.jpg', 'caption' => 'Toboáguas'],
                ],
                'videos' => [],
                'faq' => [
                    ['question' => 'A água é realmente quente?', 'answer' => 'Sim! As águas são naturalmente aquecidas a 37°C pelas nascentes termais da região.'],
                    ['question' => 'Tem restaurante?', 'answer' => 'Sim, temos praça de alimentação com diversas opções.'],
                ],
                'comfort_points' => [
                    ['type' => 'fraldario', 'label' => 'Fraldário', 'x' => 0.25, 'y' => 0.35],
                    ['type' => 'sombra', 'label' => 'Área de Sombra', 'x' => 0.40, 'y' => 0.60],
                    ['type' => 'alimentacao', 'label' => 'Restaurante', 'x' => 0.70, 'y' => 0.45],
                ],
            ],

            // Thermas dos Laranjais - São Paulo
            [
                'city' => 'Olímpia',
                'name' => 'Thermas dos Laranjais',
                'description' => 'O Thermas dos Laranjais é o maior parque aquático da América Latina em número de visitantes, com águas naturalmente quentes e mais de 55 atrações para toda a família.',
                'hero_image' => 'https://cdn.aquaguia.com/parks/thermas-laranjais/hero.jpg',
                'latitude' => -20.7367,
                'longitude' => -48.9023,
                'opening_hours' => 'Seg-Dom: 08h às 18h',
                'price_adult' => 150.00,
                'price_child' => 95.00,
                'price_parking' => 25.00,
                'price_locker' => 20.00,
                'water_heated_areas' => 12,
                'shade_level' => 'média',
                'family_index' => 5,
                'best_for' => ['Toda a família', 'Grupos grandes', 'Crianças de todas as idades'],
                'not_for' => [],
                'anti_queue_tips' => ['Chegue às 8h para aproveitar o parque vazio', 'Terça e quarta são os dias mais vazios'],
                'tags' => ['aquecida', 'bebes-ok', 'economico', 'estacionamento'],
                'attractions' => [
                    ['name' => 'Surf Station', 'type' => 'radical', 'min_height_cm' => 110, 'adrenaline' => 4, 'avg_queue_minutes' => 40, 'description' => 'Simulador de surf com ondas artificiais.'],
                    ['name' => 'Ilha dos Piratas', 'type' => 'infantil', 'min_height_cm' => 0, 'max_height_cm' => 130, 'adrenaline' => 2, 'avg_queue_minutes' => 10, 'description' => 'Playground aquático temático com toboáguas infantis.'],
                    ['name' => 'Correnteza', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Rio de correnteza com 800m de extensão.', 'has_double_float' => true],
                    ['name' => 'Acqua Race', 'type' => 'radical', 'min_height_cm' => 120, 'adrenaline' => 4, 'avg_queue_minutes' => 30, 'description' => 'Corrida de toboáguas com cronômetro.'],
                    ['name' => 'Praia Termal', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Praia artificial com águas termais.'],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/thermas-laranjais/vista.jpg', 'caption' => 'Vista geral do parque'],
                    ['url' => 'https://cdn.aquaguia.com/parks/thermas-laranjais/surf.jpg', 'caption' => 'Surf Station'],
                ],
                'videos' => [
                    ['youtube_id' => 'abc123', 'title' => 'Um dia no Thermas dos Laranjais'],
                ],
                'faq' => [
                    ['question' => 'Qual a temperatura da água?', 'answer' => 'A água é naturalmente aquecida entre 28°C e 37°C.'],
                    ['question' => 'Tem hospedagem?', 'answer' => 'O parque não tem hotel próprio, mas há diversas opções na região.'],
                ],
                'comfort_points' => [
                    ['type' => 'fraldario', 'label' => 'Fraldário 1', 'x' => 0.20, 'y' => 0.30],
                    ['type' => 'fraldario', 'label' => 'Fraldário 2', 'x' => 0.80, 'y' => 0.70],
                    ['type' => 'enfermaria', 'label' => 'Enfermaria', 'x' => 0.50, 'y' => 0.20],
                    ['type' => 'alimentacao', 'label' => 'Praça de Alimentação', 'x' => 0.55, 'y' => 0.50],
                ],
            ],

            // Beto Carrero World - Santa Catarina
            [
                'city' => 'Penha',
                'name' => 'Beto Carrero World',
                'description' => 'O maior parque temático da América Latina, com área aquática completa incluindo a Acqua World. Combina montanhas-russas, shows e atrações aquáticas.',
                'hero_image' => 'https://cdn.aquaguia.com/parks/beto-carrero/hero.jpg',
                'latitude' => -26.8019,
                'longitude' => -48.6292,
                'opening_hours' => 'Qui-Dom: 09h às 18h',
                'price_adult' => 189.00,
                'price_child' => 149.00,
                'price_parking' => 40.00,
                'price_locker' => 30.00,
                'water_heated_areas' => 2,
                'shade_level' => 'média',
                'family_index' => 4,
                'best_for' => ['Famílias', 'Fãs de parques temáticos', 'Quem quer variedade'],
                'not_for' => ['Quem busca apenas parque aquático'],
                'anti_queue_tips' => ['Compre ingresso antecipado', 'Vá direto para as atrações mais populares'],
                'tags' => ['radical', 'estacionamento', 'resort'],
                'attractions' => [
                    ['name' => 'Acqua World - Tornado', 'type' => 'radical', 'min_height_cm' => 130, 'adrenaline' => 5, 'avg_queue_minutes' => 50, 'description' => 'Toboágua funil com descida em grupo.'],
                    ['name' => 'Acqua World - Rio Bravo', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 2, 'avg_queue_minutes' => 15, 'description' => 'Boia coletiva em rio artificial.', 'has_double_float' => true],
                    ['name' => 'Acqua World Kids', 'type' => 'infantil', 'min_height_cm' => 0, 'max_height_cm' => 120, 'adrenaline' => 1, 'avg_queue_minutes' => 5, 'description' => 'Área aquática infantil.'],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/beto-carrero/acqua.jpg', 'caption' => 'Acqua World'],
                ],
                'videos' => [],
                'faq' => [
                    ['question' => 'Precisa de ingresso separado para área aquática?', 'answer' => 'Não, o ingresso dá acesso a todo o parque.'],
                ],
                'comfort_points' => [
                    ['type' => 'alimentacao', 'label' => 'Restaurante Náutico', 'x' => 0.45, 'y' => 0.55],
                ],
            ],

            // Rio Quente Resorts - Goiás
            [
                'city' => 'Rio Quente',
                'name' => 'Hot Park',
                'description' => 'O Hot Park faz parte do complexo Rio Quente Resorts e oferece o maior rio de correnteza aquecido do mundo, além de piscinas naturalmente quentes e toboáguas.',
                'hero_image' => 'https://cdn.aquaguia.com/parks/hot-park/hero.jpg',
                'latitude' => -17.7756,
                'longitude' => -48.7606,
                'opening_hours' => 'Seg-Dom: 09h às 17h',
                'price_adult' => 220.00,
                'price_child' => 165.00,
                'price_parking' => 0.00,
                'price_locker' => 0.00,
                'water_heated_areas' => 15,
                'shade_level' => 'alta',
                'family_index' => 5,
                'best_for' => ['Famílias com crianças', 'Idosos', 'Lua de mel', 'Quem busca relaxamento'],
                'not_for' => ['Quem busca atrações extremas'],
                'anti_queue_tips' => ['Hóspedes do resort têm acesso exclusivo pela manhã', 'Visite no inverno para aproveitar a água quente'],
                'tags' => ['aquecida', 'bebes-ok', 'sombra-boa', 'resort', 'acessibilidade'],
                'attractions' => [
                    ['name' => 'Praia do Cerrado', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Maior praia artificial de água quente do mundo.'],
                    ['name' => 'Rio Quente', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Rio de correnteza natural com águas termais.', 'has_double_float' => true],
                    ['name' => 'Half Pipe', 'type' => 'radical', 'min_height_cm' => 120, 'adrenaline' => 4, 'avg_queue_minutes' => 25, 'description' => 'Toboágua que simula uma rampa de skate.'],
                    ['name' => 'Hot Lake', 'type' => 'infantil', 'min_height_cm' => 0, 'max_height_cm' => 130, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Lago infantil com temperatura controlada.'],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/hot-park/praia-cerrado.jpg', 'caption' => 'Praia do Cerrado'],
                    ['url' => 'https://cdn.aquaguia.com/parks/hot-park/rio.jpg', 'caption' => 'Rio de correnteza'],
                ],
                'videos' => [
                    ['youtube_id' => 'xyz789', 'title' => 'Hot Park - Experiência completa'],
                ],
                'faq' => [
                    ['question' => 'A água é naturalmente quente?', 'answer' => 'Sim! As águas são aquecidas naturalmente pelo lençol freático, mantendo temperatura entre 32°C e 42°C.'],
                    ['question' => 'Precisa ser hóspede?', 'answer' => 'Não, o parque aceita visitantes day-use com ingresso.'],
                ],
                'comfort_points' => [
                    ['type' => 'fraldario', 'label' => 'Fraldário Premium', 'x' => 0.30, 'y' => 0.40],
                    ['type' => 'enfermaria', 'label' => 'Enfermaria', 'x' => 0.50, 'y' => 0.25],
                    ['type' => 'alimentacao', 'label' => 'Restaurante Gourmet', 'x' => 0.65, 'y' => 0.60],
                    ['type' => 'sombra', 'label' => 'Cabanas VIP', 'x' => 0.75, 'y' => 0.35],
                ],
            ],

            // Wet'n Wild - São Paulo
            [
                'city' => 'Campinas',
                'name' => "Wet'n Wild",
                'description' => "O Wet'n Wild é um dos parques aquáticos mais tradicionais do Brasil, localizado em Itupeva (região de Campinas). Oferece atrações radicais e áreas de lazer.",
                'hero_image' => 'https://cdn.aquaguia.com/parks/wetnwild/hero.jpg',
                'latitude' => -23.1458,
                'longitude' => -47.0322,
                'opening_hours' => 'Sab-Dom: 10h às 17h',
                'price_adult' => 149.00,
                'price_child' => 99.00,
                'price_parking' => 35.00,
                'price_locker' => 25.00,
                'water_heated_areas' => 0,
                'shade_level' => 'baixa',
                'family_index' => 3,
                'best_for' => ['Grupos de amigos', 'Adolescentes', 'Casais jovens'],
                'not_for' => ['Famílias com bebês', 'Idosos'],
                'anti_queue_tips' => ['Use o Fast Pass', 'Vá em dias nublados (menos lotado)'],
                'tags' => ['radical', 'estacionamento'],
                'attractions' => [
                    ['name' => 'Extreme', 'type' => 'radical', 'min_height_cm' => 140, 'adrenaline' => 5, 'avg_queue_minutes' => 40, 'description' => 'Queda livre em toboágua com 25 metros de altura.'],
                    ['name' => 'Tornado', 'type' => 'radical', 'min_height_cm' => 130, 'adrenaline' => 5, 'avg_queue_minutes' => 35, 'description' => 'Funil gigante para descida em boia.'],
                    ['name' => 'Wave Pool', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 2, 'avg_queue_minutes' => 5, 'description' => 'Piscina de ondas gigante.'],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/wetnwild/tornado.jpg', 'caption' => 'Tornado'],
                ],
                'videos' => [],
                'faq' => [
                    ['question' => 'Funciona no inverno?', 'answer' => 'Sim, mas recomendamos visitar em dias mais quentes.'],
                ],
                'comfort_points' => [
                    ['type' => 'alimentacao', 'label' => 'Lanchonete', 'x' => 0.50, 'y' => 0.50],
                ],
            ],

            // Acquamania - Santa Catarina
            [
                'city' => 'Florianópolis',
                'name' => 'Acquamania',
                'description' => 'O Acquamania é o único parque aquático de Florianópolis, oferecendo diversão para toda a família em um ambiente acolhedor com vista para morros.',
                'hero_image' => 'https://cdn.aquaguia.com/parks/acquamania/hero.jpg',
                'latitude' => -27.5945,
                'longitude' => -48.5477,
                'opening_hours' => 'Seg-Dom: 10h às 18h',
                'price_adult' => 89.00,
                'price_child' => 59.00,
                'price_parking' => 0.00,
                'price_locker' => 15.00,
                'water_heated_areas' => 3,
                'shade_level' => 'média',
                'family_index' => 4,
                'best_for' => ['Famílias locais', 'Turistas na ilha', 'Crianças'],
                'not_for' => ['Quem busca parques gigantes'],
                'anti_queue_tips' => ['Visite durante a semana no verão', 'Período da manhã é mais tranquilo'],
                'tags' => ['aquecida', 'bebes-ok', 'economico'],
                'attractions' => [
                    ['name' => 'Kamikaze', 'type' => 'radical', 'min_height_cm' => 130, 'adrenaline' => 4, 'avg_queue_minutes' => 20, 'description' => 'Toboágua de alta velocidade.'],
                    ['name' => 'Piscina Infantil Aquecida', 'type' => 'infantil', 'min_height_cm' => 0, 'max_height_cm' => 120, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Piscina aquecida exclusiva para crianças.'],
                    ['name' => 'Rio Tropical', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Rio de correnteza em ambiente tropical.', 'has_double_float' => true],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/acquamania/parque.jpg', 'caption' => 'Vista do parque'],
                ],
                'videos' => [],
                'faq' => [
                    ['question' => 'Tem cabanas?', 'answer' => 'Sim, oferecemos cabanas para aluguel com cadeiras e guarda-sol.'],
                ],
                'comfort_points' => [
                    ['type' => 'fraldario', 'label' => 'Fraldário', 'x' => 0.40, 'y' => 0.55],
                    ['type' => 'alimentacao', 'label' => 'Restaurante', 'x' => 0.60, 'y' => 0.45],
                ],
            ],

            // DiRoma Acqua Park - Goiás
            [
                'city' => 'Caldas Novas',
                'name' => 'DiRoma Acqua Park',
                'description' => 'Um dos maiores complexos de água quente do Brasil, o DiRoma oferece piscinas com águas naturalmente aquecidas e infraestrutura completa.',
                'hero_image' => 'https://cdn.aquaguia.com/parks/diroma/hero.jpg',
                'latitude' => -17.7356,
                'longitude' => -48.6189,
                'opening_hours' => 'Seg-Dom: 08h às 18h',
                'price_adult' => 110.00,
                'price_child' => 70.00,
                'price_parking' => 20.00,
                'price_locker' => 15.00,
                'water_heated_areas' => 10,
                'shade_level' => 'alta',
                'family_index' => 5,
                'best_for' => ['Famílias', 'Idosos', 'Grupos grandes'],
                'not_for' => [],
                'anti_queue_tips' => ['Evite a alta temporada de julho', 'Segundas são os dias mais tranquilos'],
                'tags' => ['aquecida', 'bebes-ok', 'sombra-boa', 'economico', 'estacionamento'],
                'attractions' => [
                    ['name' => 'Piscina de Ondas Aquecida', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 2, 'avg_queue_minutes' => 5, 'description' => 'Maior piscina de ondas com água quente da região.'],
                    ['name' => 'Toboáguas Aquecidos', 'type' => 'família', 'min_height_cm' => 100, 'adrenaline' => 3, 'avg_queue_minutes' => 15, 'description' => 'Conjunto de toboáguas com água a 35°C.'],
                    ['name' => 'Ofurô Natural', 'type' => 'família', 'min_height_cm' => 0, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Banheiras naturais com água a 40°C.'],
                    ['name' => 'Kids Termal', 'type' => 'infantil', 'min_height_cm' => 0, 'max_height_cm' => 120, 'adrenaline' => 1, 'avg_queue_minutes' => 0, 'description' => 'Área infantil com água aquecida e playground.'],
                ],
                'photos' => [
                    ['url' => 'https://cdn.aquaguia.com/parks/diroma/piscinas.jpg', 'caption' => 'Piscinas termais'],
                    ['url' => 'https://cdn.aquaguia.com/parks/diroma/toboaguas.jpg', 'caption' => 'Toboáguas'],
                ],
                'videos' => [],
                'faq' => [
                    ['question' => 'Qual a temperatura da água?', 'answer' => 'Entre 32°C e 42°C, dependendo da piscina.'],
                ],
                'comfort_points' => [
                    ['type' => 'fraldario', 'label' => 'Fraldário', 'x' => 0.25, 'y' => 0.40],
                    ['type' => 'sombra', 'label' => 'Área de Descanso', 'x' => 0.50, 'y' => 0.70],
                    ['type' => 'alimentacao', 'label' => 'Restaurante', 'x' => 0.75, 'y' => 0.35],
                    ['type' => 'bebedouro', 'label' => 'Bebedouros', 'x' => 0.40, 'y' => 0.55],
                ],
            ],
        ];

        foreach ($parks as $parkData) {
            $city = City::where('name', $parkData['city'])->first();
            if (!$city) {
                $this->command->warn("Cidade não encontrada: {$parkData['city']}");
                continue;
            }

            // Create park
            $park = Park::firstOrCreate(
                ['name' => $parkData['name']],
                [
                    'slug' => Str::slug($parkData['name']),
                    'city_id' => $city->id,
                    'description' => $parkData['description'],
                    'hero_image' => $parkData['hero_image'],
                    'latitude' => $parkData['latitude'],
                    'longitude' => $parkData['longitude'],
                    'opening_hours' => $parkData['opening_hours'],
                    'price_adult' => $parkData['price_adult'],
                    'price_child' => $parkData['price_child'],
                    'price_parking' => $parkData['price_parking'],
                    'price_locker' => $parkData['price_locker'],
                    'water_heated_areas' => $parkData['water_heated_areas'],
                    'shade_level' => $parkData['shade_level'],
                    'family_index' => $parkData['family_index'],
                    'best_for' => $parkData['best_for'],
                    'not_for' => $parkData['not_for'],
                    'anti_queue_tips' => $parkData['anti_queue_tips'],
                    'is_active' => true,
                ]
            );

            // Attach tags
            $tagIds = Tag::whereIn('slug', $parkData['tags'])->pluck('id');
            $park->tags()->sync($tagIds);

            // Create attractions
            $order = 0;
            foreach ($parkData['attractions'] as $attractionData) {
                Attraction::firstOrCreate(
                    ['park_id' => $park->id, 'name' => $attractionData['name']],
                    [
                        'type' => $attractionData['type'],
                        'min_height_cm' => $attractionData['min_height_cm'],
                        'max_height_cm' => $attractionData['max_height_cm'] ?? null,
                        'adrenaline' => $attractionData['adrenaline'],
                        'avg_queue_minutes' => $attractionData['avg_queue_minutes'],
                        'description' => $attractionData['description'],
                        'has_double_float' => $attractionData['has_double_float'] ?? false,
                        'is_open' => true,
                        'display_order' => $order++,
                    ]
                );
            }

            // Create photos
            $order = 0;
            foreach ($parkData['photos'] as $photoData) {
                ParkPhoto::firstOrCreate(
                    ['park_id' => $park->id, 'url' => $photoData['url']],
                    [
                        'caption' => $photoData['caption'],
                        'display_order' => $order++,
                    ]
                );
            }

            // Create videos
            $order = 0;
            foreach ($parkData['videos'] as $videoData) {
                ParkVideo::firstOrCreate(
                    ['park_id' => $park->id, 'youtube_id' => $videoData['youtube_id']],
                    [
                        'title' => $videoData['title'],
                        'display_order' => $order++,
                    ]
                );
            }

            // Create FAQ
            $order = 0;
            foreach ($parkData['faq'] as $faqData) {
                ParkFaq::firstOrCreate(
                    ['park_id' => $park->id, 'question' => $faqData['question']],
                    [
                        'answer' => $faqData['answer'],
                        'display_order' => $order++,
                    ]
                );
            }

            // Create comfort points
            foreach ($parkData['comfort_points'] as $pointData) {
                ComfortPoint::firstOrCreate(
                    ['park_id' => $park->id, 'label' => $pointData['label']],
                    [
                        'type' => $pointData['type'],
                        'x' => $pointData['x'],
                        'y' => $pointData['y'],
                    ]
                );
            }

            $this->command->info("  ✓ {$park->name}");
        }
    }
}
