<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::orderBy('label')->get();

        return response()->json([
            'success' => true,
            'data' => TagResource::collection($tags),
        ]);
    }
}
