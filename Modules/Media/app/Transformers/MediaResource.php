<?php

namespace Modules\Media\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'name' => $this->name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'collection_name' => $this->collection_name,
            'url' => $this->getUrl(),
            'thumbnail_url' => $this->hasGeneratedConversion('thumbnail') ? $this->getUrl('thumbnail') : null,
            'preview_url' => $this->hasGeneratedConversion('preview') ? $this->getUrl('preview') : null,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
