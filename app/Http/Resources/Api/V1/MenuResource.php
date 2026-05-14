<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'location' => $this->location,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'items_count' => $this->whenCounted('items'),
            'items' => $this->whenLoaded('rootItems', fn () => MenuItemResource::collection($this->rootItems)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
