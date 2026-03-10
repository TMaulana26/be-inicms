<?php

namespace App\Http\Requests\Menu;

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
            'items' => 'nullable|array',
            'items.*.title' => 'required|string|max:255',
            'items.*.icon' => 'nullable|string|max:255',
            'items.*.url' => 'nullable|string',
            'items.*.target' => 'nullable|string|in:_self,_blank',
            'items.*.order' => 'nullable|integer',
            'items.*.children' => 'nullable|array',
        ];
    }
}
