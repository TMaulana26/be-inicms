<?php

namespace Modules\Menu\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'title' => $this->title,
            'icon' => $this->icon,
            'description' => $this->description,
            'url' => $this->url,
            'target' => $this->target,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'children' => MenuResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
