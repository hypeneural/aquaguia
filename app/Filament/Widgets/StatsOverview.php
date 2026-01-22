<?php

namespace App\Filament\Widgets;

use App\Models\City;
use App\Models\Park;
use App\Models\Review;
use App\Models\State;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Cache stats for 5 minutes for better performance
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'parks' => Park::count(),
                'active_parks' => Park::where('is_active', true)->count(),
                'cities' => City::has('parks')->count(),
                'states' => State::has('cities.parks')->count(),
                'reviews' => Review::count(),
                'avg_rating' => Review::avg('rating') ?? 0,
            ];
        });

        return [
            Stat::make('Total de Parques', $stats['parks'])
                ->description($stats['active_parks'] . ' ativos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                ->icon('heroicon-o-sparkles'),

            Stat::make('Cidades com Parques', $stats['cities'])
                ->description('Em ' . $stats['states'] . ' estados')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Avaliações', $stats['reviews'])
                ->description('Média: ' . number_format($stats['avg_rating'], 1) . ' ⭐')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->icon('heroicon-o-star'),
        ];
    }
}
