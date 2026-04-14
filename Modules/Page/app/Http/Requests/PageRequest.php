<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
        $page = $this->route('page');
        $id = is_object($page) ? $page->id : $page;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                $id ? 'unique:pages,title,' . $id : 'unique:pages,title',
            ],
            'content' => ['required', 'string'],
            'status' => ['required', 'string', 'in:draft,published'],
            'page_image' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }
}
