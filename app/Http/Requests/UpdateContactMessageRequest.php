<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                'contacto_inicial',
                'seguimiento',
                'contrato_cerrado',
                'contrato_pagado',
            ])],
            'cotizacion_final' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }
}