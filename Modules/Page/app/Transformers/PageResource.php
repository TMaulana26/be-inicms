<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ],
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'translations' => $this->when($request->boolean('with_translations'), $this->getTranslations()),
            'status' => $this->status,
            'page_image' => $this->getFirstMediaUrl('page_image'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
