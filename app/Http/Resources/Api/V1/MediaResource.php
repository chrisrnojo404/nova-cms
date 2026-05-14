<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'disk' => $this->disk,
            'directory' => $this->directory,
            'filename' => $this->filename,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'path' => $this->path,
            'url' => $this->url,
            'size' => $this->size,
            'alt_text' => $this->alt_text,
            'is_image' => $this->isImage(),
            'uploaded_by' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
