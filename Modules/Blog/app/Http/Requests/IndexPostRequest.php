<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'trashed' => 'nullable|in:only,with',
            'per_page' => 'nullable|integer|min:-1|max:1000',
            'sort_by' => 'nullable|string|in:id,title,slug,status,is_featured,published_at,created_at,updated_at',
            'sort_order' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:draft,published',
            'is_featured' => 'nullable|boolean',
        ];
    }
}
