<?php

namespace App\Filament\Widgets;

use App\Models\Park;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ParksByStateChart extends ChartWidget
{
    protected static ?string $heading = 'Parques por Estado';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Cache for 5 minutes
        $data = Cache::remember('parks_by_state_chart', 300, function () {
            return Park::query()
                ->join('cities', 'parks.city_id', '=', 'cities.id')
                ->join('states', 'cities.state_id', '=', 'states.id')
                ->select('states.abbr as state', DB::raw('count(*) as total'))
                ->where('parks.is_active', true)
                ->groupBy('states.abbr')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Parques',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#EC4899',
                        '#06B6D4',
                        '#84CC16',
                        '#F97316',
                        '#6366F1',
                    ],
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $data->pluck('state')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
