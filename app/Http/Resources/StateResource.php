<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'abbr' => $this->abbr,
            'name' => $this->name,
            'parkCount' => $this->parks_count ?? $this->parks()->count(),
        ];
    }
}
