<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTalentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $talentId = $this->route('talent')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('talents', 'slug')->ignore($talentId)],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'content' => ['nullable', 'string'],
            'summary' => ['nullable', 'string', 'max:500'],
            'spotify_url' => ['nullable', 'url', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'destacado' => ['nullable', 'boolean'],
            'honorarios_default' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'highlights' => ['nullable', 'array'],
            'highlights.*' => ['nullable', 'string', 'max:255'],
            'new_images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:talent_images,id'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}