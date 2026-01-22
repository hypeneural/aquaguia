<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = City::with('state')
            ->withCount('parks');

        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        if ($request->has('state')) {
            $query->whereHas('state', function ($q) use ($request) {
                $q->where('abbr', strtoupper($request->state));
            });
        }

        $limit = min((int) $request->get('limit', 20), 100);
        $cities = $query->orderBy('name')->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => CityResource::collection($cities->items()),
            'pagination' => [
                'page' => $cities->currentPage(),
                'limit' => $cities->perPage(),
                'total' => $cities->total(),
                'totalPages' => $cities->lastPage(),
                'hasNext' => $cities->hasMorePages(),
                'hasPrev' => $cities->currentPage() > 1,
            ],
        ]);
    }
}
