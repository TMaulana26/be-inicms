<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authentication handled by middleware
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:5120', // Max 5MB
                'mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx,xls,xlsx'
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'collection' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.max' => 'The file size must not exceed 5MB.',
            'file.mimes' => 'Only standard image and document formats are allowed.',
        ];
    }
}
