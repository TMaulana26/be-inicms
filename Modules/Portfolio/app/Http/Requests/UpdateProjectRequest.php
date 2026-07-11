<?php

namespace Modules\Portfolio\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $project = $this->route('project');
        $projectId = is_object($project) ? $project->id : $project;

        return [
            'title' => 'sometimes|required|array',
            'title.en' => 'required_with:title|string|max:255',
            'title.id' => 'required_with:title|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:portfolio_projects,slug,' . ($projectId ?? 'NULL'),
            'category' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|array',
            'description.en' => 'required_with:description|string',
            'description.id' => 'required_with:description|string',
            'tech_stack' => 'sometimes|required|array',
            'tech_stack.*' => 'required|string',
            'github_url' => 'nullable|url|max:255',
            'demo_url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
            'screenshot' => 'nullable|image|max:5120',
        ];
    }
}
