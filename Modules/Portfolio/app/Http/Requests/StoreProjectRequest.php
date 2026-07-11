<?php

namespace Modules\Portfolio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.id' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:portfolio_projects,slug',
            'category' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.id' => 'required|string',
            'tech_stack' => 'required|array',
            'tech_stack.*' => 'required|string',
            'github_url' => 'nullable|url|max:255',
            'demo_url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
            'screenshot' => 'nullable|image|max:5120',
        ];
    }
}
