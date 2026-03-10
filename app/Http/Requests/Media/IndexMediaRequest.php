<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class IndexMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('only_profile_picture')) {
            $this->merge([
                'only_profile_picture' => filter_var($this->only_profile_picture, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'trashed' => 'nullable|in:only,with',
            'per_page' => 'nullable|integer|min:-1|max:1000',
            'sort_by' => 'nullable|string|in:id,name,file_name,created_at,updated_at',
            'sort_order' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
            'only_profile_picture' => 'nullable|boolean',
        ];
    }
}
