<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Http\JsonResponse;

class StateController extends Controller
{
    public function index(): JsonResponse
    {
        $states = State::withCount('parks')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => StateResource::collection($states),
        ]);
    }

    public function cities(string $abbr): JsonResponse
    {
        $state = State::where('abbr', strtoupper($abbr))->first();

        if (!$state) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'STATE_NOT_FOUND',
                    'message' => 'Estado não encontrado',
                ],
            ], 404);
        }

        $cities = $state->cities()
            ->withCount('parks')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CityResource::collection($cities),
        ]);
    }
}
