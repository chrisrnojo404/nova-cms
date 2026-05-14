<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'linked_type' => $this->linked_type,
            'linked_id' => $this->linked_id,
            'title' => $this->title,
            'resolved_title' => $this->resolved_title,
            'url' => $this->url,
            'resolved_url' => $this->resolved_url,
            'target' => $this->target,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'children' => $this->whenLoaded('children', fn () => MenuItemResource::collection($this->children)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
