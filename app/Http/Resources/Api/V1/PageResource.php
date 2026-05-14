<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
            'template' => $this->template,
            'featured_image' => $this->featured_image,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'blocks' => $this->blocks,
            'published_at' => $this->published_at?->toIso8601String(),
            'author' => $this->whenLoaded('author', fn () => new UserResource($this->author)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
