<?php

namespace Modules\Skill\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'trashed' => 'nullable|string|in:with,only',
            'is_active' => 'nullable|string|max:10',
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|string|max:255',
            'sort_order' => 'nullable|string|in:asc,desc,ASC,DESC',
        ];
    }
}
