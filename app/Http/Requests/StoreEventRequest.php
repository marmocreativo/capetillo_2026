<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('events', 'slug')->ignore($eventId)],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'servicio_titulo' => ['nullable', 'array'],
            'servicio_titulo.*' => ['nullable', 'string', 'max:255'],
            'servicio_descripcion' => ['nullable', 'array'],
            'servicio_descripcion.*' => ['nullable', 'string', 'max:1000'],
            'faq_pregunta' => ['nullable', 'array'],
            'faq_pregunta.*' => ['nullable', 'string', 'max:255'],
            'faq_respuesta' => ['nullable', 'array'],
            'faq_respuesta.*' => ['nullable', 'string', 'max:1500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'new_images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:event_images,id'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}