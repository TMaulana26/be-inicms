<?php

namespace Modules\Menu\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:menus,name',
            'slug' => 'nullable|string|max:255|unique:menus,slug',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'children' => 'nullable|array',
            'children.*.title' => 'required|string|max:255',
            'children.*.icon' => 'nullable|string|max:255',
            'children.*.url' => 'nullable|string',
            'children.*.target' => 'nullable|string|in:_self,_blank',
            'children.*.order' => 'nullable|integer',
            'children.*.children' => 'nullable|array',
        ];
    }
}
