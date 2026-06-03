<?php

namespace Modules\Dashboard\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'users' => $this->resource['users'],
            'roles' => $this->resource['roles'],
            'permissions' => $this->resource['permissions'],
            'media' => $this->resource['media'],
            'categories' => $this->resource['categories'],
            'posts' => $this->resource['posts'],
            'pages' => $this->resource['pages'],
            'menus' => $this->resource['menus'],
            'settings' => $this->resource['settings'],
        ];
    }
}
