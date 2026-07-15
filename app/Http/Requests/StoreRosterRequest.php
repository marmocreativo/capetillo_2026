<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRosterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'intro_text' => ['nullable', 'string'],
            'outro_text' => ['nullable', 'string'],
            'separar_por_categoria' => ['nullable', 'boolean'],
            'mostrar_honorarios' => ['nullable', 'boolean'],
            'talentos_por_pagina' => ['required', 'integer', 'in:0,1,2,4'],
            'selected_logos' => ['nullable', 'array'],
            'selected_logos.*' => ['string', 'exists:logos_roster,image'],
            'contacto_tipo' => ['nullable', 'array'],
            'contacto_tipo.*' => ['nullable', 'string', 'in:email,telefono,direccion'],
            'contacto_valor' => ['nullable', 'array'],
            'contacto_valor.*' => ['nullable', 'string', 'max:255'],
            'talents' => ['nullable', 'array'],
            'talents.*' => ['exists:talents,id'],
        ];
    }
}