<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
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
            'menu_id' => $this->menu_id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'icon' => $this->icon,
            'url' => $this->url,
            'target' => $this->target,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'children' => MenuItemResource::collection($this->whenLoaded('children')),
        ];
    }
}
