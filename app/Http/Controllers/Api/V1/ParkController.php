<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParkDetailResource;
use App\Http\Resources\ParkListResource;
use App\Http\Traits\HasCursorPagination;
use App\Http\Traits\HasFieldSelection;
use App\Models\Park;
use App\Models\Tag;
use App\Models\UserFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ParkController extends Controller
{
    use HasFieldSelection, HasCursorPagination;
    /**
     * GET /parks
     * 
     * List parks with advanced filters, pagination, and available filter metadata.
     */
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
                    $q->where(function ($qq) use ($value) {
                        $qq->where('name', 'LIKE', "%{$value}%")
                            ->orWhere('description', 'LIKE', "%{$value}%");
                    });
                }),
                // NEW: Rating filter
                AllowedFilter::callback('min_rating', fn($q, $value) => $q->where('rating', '>=', (float) $value)),
                // NEW: Open now filter (requires operating hours)
                AllowedFilter::callback('open_now', function ($q, $value) {
                    if (filter_var($value, FILTER_VALIDATE_BOOL)) {
                        $q->whereHas('operatingHours', function ($qq) {
                            $dayOfWeek = strtolower(now()->englishDayOfWeek);
                            $currentTime = now()->format('H:i:s');
                            $qq->where('day_of_week', $dayOfWeek)
                                ->where('is_closed', false)
                                ->where('open_time', '<=', $currentTime)
                                ->where('close_time', '>=', $currentTime);
                        });
                    }
                }),
                // NEW: Accessibility filter
                AllowedFilter::callback('has_accessibility', function ($q, $value) {
                    if (filter_var($value, FILTER_VALIDATE_BOOL)) {
                        $q->whereNotNull('accessibility_features')
                            ->whereRaw('JSON_LENGTH(accessibility_features) > 0');
                    }
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

        // Build response
        $response = [
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
        ];

        // Add available filters if requested
        if ($request->boolean('withFilters')) {
            $response['filters'] = $this->getAvailableFilters();
        }

        return response()->json($response);
    }

    /**
     * GET /parks/cursor
     * 
     * Cursor-based pagination for infinite scroll.
     * More efficient for deep pagination than offset-based.
     * 
     * Params:
     *   - cursor: base64 encoded cursor from previous response
     *   - limit: number of items (default 20, max 100)
     *   - fields: comma-separated fields to include (optional)
     */
    public function indexCursor(Request $request): JsonResponse
    {
        $query = Park::query()
            ->with(['city.state', 'tags'])
            ->where('is_active', true);

        // Apply field selection if requested
        $allowedFields = [
            'id',
            'slug',
            'name',
            'hero_image',
            'city_id',
            'price_adult',
            'price_child',
            'family_index',
            'rating',
            'shade_level',
            'water_heated_areas',
            'latitude',
            'longitude',
        ];

        if ($request->has('fields')) {
            $query = $this->applyFieldSelection($query, $request, $allowedFields);
        }

        // Apply basic filters
        if ($request->filled('state')) {
            $query->whereHas('city.state', fn($q) => $q->where('abbr', strtoupper($request->get('state'))));
        }

        if ($request->filled('city')) {
            $query->whereHas('city', fn($q) => $q->where('slug', $request->get('city')));
        }

        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', (float) $request->get('min_rating'));
        }

        // Apply cursor pagination
        $result = $this->applyCursorPagination($query, $request, 'id', 20);

        return response()->json([
            'success' => true,
            'data' => ParkListResource::collection($result['items']),
            'pagination' => $result['pagination'],
        ]);
    }

    /**
     * GET /parks/search
     * 
     * Lightweight autocomplete endpoint.
     * Returns only slug, name, and city for fast results.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $limit = min((int) $request->get('limit', 5), 10);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $parks = Park::select(['id', 'slug', 'name', 'city_id'])
            ->with('city:id,name,state_id', 'city.state:id,abbr')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('slug', 'LIKE', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$query}%"])
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $parks->map(fn($park) => [
                'slug' => $park->slug,
                'name' => $park->name,
                'city' => $park->city->name . ' - ' . $park->city->state->abbr,
            ]),
        ]);
    }

    /**
     * GET /parks/{identifier}
     * 
     * Get park details. Supports both slug and UUID.
     * Use ?include= to load only specific relations.
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        // Find by slug or UUID
        $park = $this->findParkByIdentifier($identifier);

        if (!$park || !$park->is_active) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PARK_NOT_FOUND',
                    'message' => 'Parque não encontrado',
                ],
            ], 404);
        }

        // Determine which relations to load
        $relations = $this->getRelationsToLoad($request);
        $park->load($relations);

        // Add isFavorited if authenticated
        $isFavorited = null;
        if ($user = Auth::guard('api')->user()) {
            $isFavorited = UserFavorite::where('user_id', $user->id)
                ->where('park_id', $park->id)
                ->exists();
        }

        $resource = new ParkDetailResource($park);
        $data = $resource->toArray($request);

        if ($isFavorited !== null) {
            $data['isFavorited'] = $isFavorited;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Find park by slug or UUID.
     */
    private function findParkByIdentifier(string $identifier): ?Park
    {
        // Check if it's a UUID (basic validation)
        $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $identifier);

        if ($isUuid) {
            return Park::find($identifier);
        }

        return Park::where('slug', $identifier)->first();
    }

    /**
     * Get relations to load based on ?include= parameter.
     */
    private function getRelationsToLoad(Request $request): array
    {
        $defaultRelations = [
            'city.state',
            'tags',
            'attractions',
            'photos',
            'videos',
            'faq',
            'comfortPoints',
        ];

        if (!$request->has('include')) {
            return $defaultRelations;
        }

        $allowed = ['city', 'tags', 'attractions', 'photos', 'videos', 'faq', 'comfortPoints'];
        $requested = explode(',', $request->get('include'));

        $relations = ['city.state']; // Always include city
        foreach ($requested as $rel) {
            $rel = trim($rel);
            if (in_array($rel, $allowed) && $rel !== 'city') {
                $relations[] = $rel;
            }
        }

        return $relations;
    }

    /**
     * Get available filter options based on active parks.
     */
    private function getAvailableFilters(): array
    {
        $activeParks = Park::where('is_active', true);

        // Available states
        $states = DB::table('parks')
            ->join('cities', 'parks.city_id', '=', 'cities.id')
            ->join('states', 'cities.state_id', '=', 'states.id')
            ->where('parks.is_active', true)
            ->distinct()
            ->pluck('states.abbr')
            ->sort()
            ->values();

        // Available tags
        $tags = Tag::whereHas('parks', fn($q) => $q->where('is_active', true))
            ->pluck('slug')
            ->sort()
            ->values();

        // Price range
        $priceRange = $activeParks->selectRaw('MIN(price_adult) as min, MAX(price_adult) as max')->first();

        // Family index range
        $familyRange = $activeParks->selectRaw('MIN(family_index) as min, MAX(family_index) as max')->first();

        return [
            'states' => $states,
            'tags' => $tags,
            'priceRange' => [
                'min' => (float) ($priceRange->min ?? 0),
                'max' => (float) ($priceRange->max ?? 0),
            ],
            'familyIndexRange' => [
                'min' => (int) ($familyRange->min ?? 1),
                'max' => (int) ($familyRange->max ?? 5),
            ],
        ];
    }

    /**
     * GET /parks/{park}/reviews
     * 
     * Get reviews for a park with rating distribution and summary.
     */
    public function reviews(Park $park, Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 50);
        $sort = $request->get('sort', '-created_at');

        $query = $park->reviews()->with(['user:id,name,email']);

        // Sorting
        if (str_starts_with($sort, '-')) {
            $query->orderByDesc(substr($sort, 1));
        } else {
            $query->orderBy($sort);
        }

        $paginator = $query->paginate($limit)->appends($request->query());

        // Calculate summary
        $allReviews = $park->reviews();
        $totalReviews = $allReviews->count();
        $averageRating = round($allReviews->avg('rating') ?? 0, 1);

        // Rating distribution
        $distribution = $park->reviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[(string) $i] = $distribution[$i] ?? 0;
        }

        return response()->json([
            'success' => true,
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
                'hasNext' => $paginator->hasMorePages(),
            ],
            'summary' => [
                'average_rating' => $averageRating,
                'total_reviews' => $totalReviews,
                'rating_distribution' => $ratingDistribution,
            ],
        ]);
    }
}
