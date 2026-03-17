<?php

namespace Modules\Menu\Http\Requests\MenuItem;

use Illuminate\Foundation\Http\FormRequest;

class IndexMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_id' => 'nullable|exists:menus,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'search' => 'nullable|string|max:255',
            'sort_by' => 'nullable|string|in:id,title,order,created_at',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:-1',
            'trashed' => 'nullable|string|in:with,only',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }
}
