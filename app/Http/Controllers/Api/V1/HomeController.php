<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\ParkListResource;
use App\Models\Attraction;
use App\Models\City;
use App\Models\Park;
use App\Models\State;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * GET /parks/home
     * 
     * Consolidated endpoint for home page data.
     * Returns featured cities, top parks, featured attractions, and platform stats.
     */
    public function index(): JsonResponse
    {
        // Featured cities with park count
        $featuredCities = City::with('state')
            ->withCount('parks')
            ->where('featured', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        // Top parks by family index
        $topParks = Park::with(['city.state', 'tags'])
            ->where('is_active', true)
            ->orderByDesc('family_index')
            ->limit(6)
            ->get();

        // Featured attractions (highest adrenaline, with park info)
        $featuredAttractions = Attraction::with('park:id,name,slug')
            ->where('is_open', true)
            ->whereNotNull('image')
            ->orderByDesc('adrenaline')
            ->limit(10)
            ->get()
            ->map(fn($attraction) => [
                'id' => $attraction->id,
                'name' => $attraction->name,
                'park' => [
                    'id' => $attraction->park->id,
                    'name' => $attraction->park->name,
                    'slug' => $attraction->park->slug,
                ],
                'type' => $attraction->type,
                'adrenaline' => $attraction->adrenaline,
                'min_height_cm' => $attraction->min_height_cm,
                'image' => $attraction->image,
            ]);

        // Platform stats
        $stats = [
            'totalParks' => Park::where('is_active', true)->count(),
            'totalCities' => City::whereHas('parks', fn($q) => $q->where('is_active', true))->count(),
            'totalStates' => State::whereHas('cities.parks', fn($q) => $q->where('is_active', true))->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'featuredCities' => CityResource::collection($featuredCities),
                'topParks' => ParkListResource::collection($topParks),
                'featuredAttractions' => $featuredAttractions,
                'stats' => $stats,
            ],
        ]);
    }
}
