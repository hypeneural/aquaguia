<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttractionResource;
use App\Models\Park;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    public function index(Request $request, Park $park): JsonResponse
    {
        $query = $park->attractions();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('accessibleForHeight')) {
            $height = (int) $request->accessibleForHeight;
            $query->where('min_height_cm', '<=', $height)
                ->where(function ($q) use ($height) {
                    $q->whereNull('max_height_cm')
                        ->orWhere('max_height_cm', '>=', $height);
                });
        }

        if ($request->has('maxAdrenaline')) {
            $query->where('adrenaline', '<=', (int) $request->maxAdrenaline);
        }

        $attractions = $query->orderBy('display_order')->get();

        return response()->json([
            'success' => true,
            'data' => AttractionResource::collection($attractions),
        ]);
    }
}
