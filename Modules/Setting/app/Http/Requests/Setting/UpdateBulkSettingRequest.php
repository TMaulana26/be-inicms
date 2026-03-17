<?php

namespace Modules\Setting\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBulkSettingRequest extends FormRequest
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
            'settings' => 'required|array|min:1',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'nullable',
        ];
    }
}
