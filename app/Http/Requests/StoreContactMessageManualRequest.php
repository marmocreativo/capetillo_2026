<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['general', 'contratacion'])],
            'talent_id' => ['nullable', 'exists:talents,id'],
            'talent_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
            'estado_republica' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'aforo_esperado' => ['nullable', 'integer', 'min:1'],
            'venue' => ['nullable', 'string', 'max:255'],
            'tipo_evento' => ['nullable', 'string', Rule::in([
                'privado', 'corporativo', 'publico_masivo', 'social', 'gubernamental',
            ])],
            'con_venta_boletos' => ['nullable', 'boolean'],
            'tiene_presupuesto' => ['nullable', 'boolean'],
            'presupuesto_aproximado' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', Rule::in([
                'contacto_inicial', 'procesando', 'venta_no_concluida',
                'cotizacion_completa', 'contrato_cerrado', 'contrato_pagado',
            ])],
            'enviar_cotizacion' => ['nullable', 'boolean'],
        ];
    }
}