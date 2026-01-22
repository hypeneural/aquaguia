<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasFieldSelection
{
    /**
     * Apply field selection to query based on `fields` parameter.
     * 
     * Usage: GET /parks?fields=id,name,slug,heroImage,priceAdult
     */
    protected function applyFieldSelection(Builder $query, Request $request, array $allowedFields, array $alwaysInclude = ['id']): Builder
    {
        if (!$request->has('fields')) {
            return $query;
        }

        $requestedFields = array_map('trim', explode(',', $request->get('fields')));

        // Map camelCase to snake_case
        $mappedFields = array_map(function ($field) use ($allowedFields) {
            $snakeCase = $this->camelToSnake($field);
            return in_array($snakeCase, $allowedFields) ? $snakeCase : null;
        }, $requestedFields);

        // Filter valid fields and always include required ones
        $validFields = array_filter($mappedFields);
        $validFields = array_unique(array_merge($alwaysInclude, $validFields));

        return $query->select($validFields);
    }

    /**
     * Convert camelCase to snake_case.
     */
    private function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
