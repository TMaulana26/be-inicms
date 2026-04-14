<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
        $post = $this->route('post');
        $id = is_object($post) ? $post->id : $post;

        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => [
                'required',
                'string',
                'max:255',
                $id ? 'unique:posts,title,' . $id : 'unique:posts,title',
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string', 'in:draft,published'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }
}
