<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParkDetailResource;
use App\Http\Resources\ParkListResource;
use App\Models\Park;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ParkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = QueryBuilder::for(Park::class)
            ->with(['city.state', 'tags'])
            ->where('is_active', true)
            ->allowedFilters([
                AllowedFilter::exact('city_id'),
                AllowedFilter::callback('state', function ($q, $value) {
                    $q->whereHas('city.state', fn($qq) => $qq->where('abbr', strtoupper($value)));
                }),
                AllowedFilter::callback('city', function ($q, $value) {
                    $q->whereHas('city', fn($qq) => $qq->where('slug', $value));
                }),
                AllowedFilter::callback('tags', function ($q, $value) {
                    $tags = is_array($value) ? $value : [$value];
                    $q->whereHas('tags', fn($qq) => $qq->whereIn('slug', $tags));
                }),
                AllowedFilter::callback('hasHeatedWater', function ($q, $value) {
                    if (filter_var($value, FILTER_VALIDATE_BOOL)) {
                        $q->where('water_heated_areas', '>', 0);
                    }
                }),
                AllowedFilter::exact('shade_level'),
                AllowedFilter::callback('maxPrice', fn($q, $value) => $q->where('price_adult', '<=', $value)),
                AllowedFilter::callback('minFamilyIndex', fn($q, $value) => $q->where('family_index', '>=', $value)),
                AllowedFilter::callback('search', function ($q, $value) {
                    $q->whereFullText(['name', 'description'], $value);
                }),
            ])
            ->allowedSorts(['family_index', 'price_adult', 'name', 'created_at']);

        // Default sort
        if (!$request->has('sort')) {
            $query->orderByDesc('family_index');
        }

        // Distance sorting (if lat/lng provided)
        if ($request->filled(['lat', 'lng'])) {
            $lat = (float) $request->get('lat');
            $lng = (float) $request->get('lng');

            $query->selectRaw("
                parks.*,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) as distance_km
            ", [$lat, $lng, $lat]);

            if ($request->get('sort') === 'distance') {
                $query->orderBy('distance_km', $request->get('sortOrder', 'asc'));
            }

            // Max distance filter
            if ($request->filled('maxDistance')) {
                $query->having('distance_km', '<=', (float) $request->get('maxDistance'));
            }
        }

        $limit = min((int) $request->get('limit', 20), 100);
        $paginator = $query->paginate($limit)->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => ParkListResource::collection($paginator->items()),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
                'hasNext' => $paginator->hasMorePages(),
                'hasPrev' => $paginator->currentPage() > 1,
            ],
        ]);
    }

    public function show(Park $park): JsonResponse
    {
        if (!$park->is_active) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PARK_NOT_FOUND',
                    'message' => 'Parque não encontrado',
                ],
            ], 404);
        }

        $park->load([
            'city.state',
            'tags',
            'attractions',
            'photos',
            'videos',
            'faq',
            'comfortPoints',
        ]);

        return response()->json([
            'success' => true,
            'data' => new ParkDetailResource($park),
        ]);
    }
}
