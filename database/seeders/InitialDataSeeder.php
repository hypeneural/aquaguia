<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Estados brasileiros
        $states = [
            ['name' => 'Acre', 'abbr' => 'AC'],
            ['name' => 'Alagoas', 'abbr' => 'AL'],
            ['name' => 'Amapá', 'abbr' => 'AP'],
            ['name' => 'Amazonas', 'abbr' => 'AM'],
            ['name' => 'Bahia', 'abbr' => 'BA'],
            ['name' => 'Ceará', 'abbr' => 'CE'],
            ['name' => 'Distrito Federal', 'abbr' => 'DF'],
            ['name' => 'Espírito Santo', 'abbr' => 'ES'],
            ['name' => 'Goiás', 'abbr' => 'GO'],
            ['name' => 'Maranhão', 'abbr' => 'MA'],
            ['name' => 'Mato Grosso', 'abbr' => 'MT'],
            ['name' => 'Mato Grosso do Sul', 'abbr' => 'MS'],
            ['name' => 'Minas Gerais', 'abbr' => 'MG'],
            ['name' => 'Pará', 'abbr' => 'PA'],
            ['name' => 'Paraíba', 'abbr' => 'PB'],
            ['name' => 'Paraná', 'abbr' => 'PR'],
            ['name' => 'Pernambuco', 'abbr' => 'PE'],
            ['name' => 'Piauí', 'abbr' => 'PI'],
            ['name' => 'Rio de Janeiro', 'abbr' => 'RJ'],
            ['name' => 'Rio Grande do Norte', 'abbr' => 'RN'],
            ['name' => 'Rio Grande do Sul', 'abbr' => 'RS'],
            ['name' => 'Rondônia', 'abbr' => 'RO'],
            ['name' => 'Roraima', 'abbr' => 'RR'],
            ['name' => 'Santa Catarina', 'abbr' => 'SC'],
            ['name' => 'São Paulo', 'abbr' => 'SP'],
            ['name' => 'Sergipe', 'abbr' => 'SE'],
            ['name' => 'Tocantins', 'abbr' => 'TO'],
        ];

        foreach ($states as $state) {
            State::firstOrCreate(['abbr' => $state['abbr']], $state);
        }

        // Tags padrão
        $tags = [
            ['slug' => 'bebes-ok', 'label' => 'Bebês OK', 'emoji' => '👶', 'color' => '#EC4899'],
            ['slug' => 'aquecida', 'label' => 'Água Aquecida', 'emoji' => '🌡️', 'color' => '#F97316'],
            ['slug' => 'sombra-boa', 'label' => 'Boa Sombra', 'emoji' => '🌴', 'color' => '#22C55E'],
            ['slug' => 'radical', 'label' => 'Radical', 'emoji' => '🎢', 'color' => '#8B5CF6'],
            ['slug' => 'economico', 'label' => 'Econômico', 'emoji' => '💰', 'color' => '#14B8A6'],
            ['slug' => 'estacionamento', 'label' => 'Estacionamento', 'emoji' => '🅿️', 'color' => '#3B82F6'],
            ['slug' => 'acessibilidade', 'label' => 'Acessibilidade', 'emoji' => '♿', 'color' => '#6366F1'],
            ['slug' => 'resort', 'label' => 'Resort', 'emoji' => '🏨', 'color' => '#EAB308'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }

        $this->command->info('Estados e Tags criados com sucesso!');
    }
}
