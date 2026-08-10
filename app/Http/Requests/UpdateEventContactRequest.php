<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventContactRequest extends FormRequest
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
                'procesando',
                'en_espera_cotizacion',
                'venta_no_concluida',
                'cotizacion_completa',
                'contrato_cerrado',
                'contrato_pagado',
            ])],
        ];
    }
}