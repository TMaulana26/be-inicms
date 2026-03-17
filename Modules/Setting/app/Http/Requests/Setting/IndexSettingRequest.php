<?php

namespace Modules\Setting\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class IndexSettingRequest extends FormRequest
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
            'group' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',
            'trashed' => 'nullable|string|in:with,only',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }
}
