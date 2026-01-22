<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Park;
use App\Models\UserFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $favorites = UserFavorite::with(['park.city.state', 'park.tags'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => FavoriteResource::collection($favorites),
        ]);
    }

    public function store(Park $park): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $exists = UserFavorite::where('user_id', $user->id)
            ->where('park_id', $park->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ALREADY_FAVORITED',
                    'message' => 'Parque já está nos favoritos',
                ],
            ], 409);
        }

        UserFavorite::create([
            'user_id' => $user->id,
            'park_id' => $park->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parque adicionado aos favoritos',
        ], 201);
    }

    public function destroy(Park $park): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $deleted = UserFavorite::where('user_id', $user->id)
            ->where('park_id', $park->id)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FAVORITED',
                    'message' => 'Parque não está nos favoritos',
                ],
            ], 404);
        }

        return response()->json(null, 204);
    }
}
