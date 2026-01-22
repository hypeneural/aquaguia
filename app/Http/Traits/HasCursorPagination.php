<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasCursorPagination
{
    /**
     * Apply cursor-based pagination to query.
     * 
     * Usage: GET /parks?cursor=eyJpZCI6IjEwMCJ9&limit=20
     * 
     * Returns cursor for next page in response.
     */
    protected function applyCursorPagination(Builder $query, Request $request, string $cursorColumn = 'id', int $defaultLimit = 20): array
    {
        $limit = min((int) $request->get('limit', $defaultLimit), 100);
        $cursor = $request->get('cursor');
        $direction = $request->get('cursor_direction', 'next'); // 'next' or 'prev'

        // Decode cursor if provided
        if ($cursor) {
            $decodedCursor = json_decode(base64_decode($cursor), true);

            if ($decodedCursor && isset($decodedCursor['v'])) {
                $operator = $direction === 'prev' ? '<' : '>';
                $query->where($cursorColumn, $operator, $decodedCursor['v']);
            }
        }

        // Order and limit
        $orderDirection = $direction === 'prev' ? 'desc' : 'asc';
        $query->orderBy($cursorColumn, $orderDirection)->limit($limit + 1);

        // Execute query
        $items = $query->get();

        // Determine if there are more results
        $hasMore = $items->count() > $limit;

        if ($hasMore) {
            $items = $items->take($limit);
        }

        // Generate next cursor
        $nextCursor = null;
        if ($hasMore && $items->isNotEmpty()) {
            $lastItem = $items->last();
            $nextCursor = base64_encode(json_encode(['v' => $lastItem->{$cursorColumn}]));
        }

        // Generate prev cursor (if not first page)
        $prevCursor = null;
        if ($cursor && $items->isNotEmpty()) {
            $firstItem = $items->first();
            $prevCursor = base64_encode(json_encode(['v' => $firstItem->{$cursorColumn}]));
        }

        return [
            'items' => $items,
            'pagination' => [
                'limit' => $limit,
                'has_more' => $hasMore,
                'next_cursor' => $nextCursor,
                'prev_cursor' => $prevCursor,
            ],
        ];
    }
}
