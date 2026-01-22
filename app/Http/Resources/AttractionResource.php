<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'minHeightCm' => $this->min_height_cm,
            'maxHeightCm' => $this->max_height_cm,
            'adrenaline' => $this->adrenaline,
            'avgQueueMinutes' => $this->avg_queue_minutes,
            'description' => $this->description,
            'hasDoubleFloat' => $this->has_double_float,
            'image' => $this->image,
            'isOpen' => $this->is_open,
        ];
    }
}
