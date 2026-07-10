<?php

namespace Modules\Contact\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexContactRequest extends FormRequest
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
            'search' => 'nullable|string|max:255',
            'trashed' => 'nullable|string|in:with,only',
            'status' => 'nullable|string|in:active,inactive',
            'is_read' => 'nullable|string|max:10',
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|string|max:255',
            'sort_order' => 'nullable|string|in:asc,desc,ASC,DESC',
        ];
    }
}
