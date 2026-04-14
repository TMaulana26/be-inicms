<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexPageRequest extends FormRequest
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
            'sort_by' => 'nullable|string|in:id,title,slug,status,created_at,updated_at',
            'sort_order' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published',
        ];
    }
}
