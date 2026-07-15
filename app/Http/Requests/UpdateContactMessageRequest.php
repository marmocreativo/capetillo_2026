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
                'procesando',
                'venta_no_concluida',
                'cotizacion_completa',
                'contrato_cerrado',
                'contrato_pagado',
            ])],
            'cotizacion_final' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'type' => ['required', Rule::in(['general', 'contratacion'])],
            'talent_id' => ['nullable', 'exists:talents,id'],
            'talent_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
            'estado_republica' => ['nullable', 'string', 'max:255'],
            'aforo_esperado' => ['nullable', 'integer', 'min:1'],
            'venue' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:3000'],
            'fecha_vigencia' => ['nullable', 'date'],
            'contacto_tipo' => ['nullable', 'array'],
            'contacto_tipo.*' => ['nullable', 'string', 'in:email,telefono,direccion'],
            'contacto_valor' => ['nullable', 'array'],
            'contacto_valor.*' => ['nullable', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'puesto_contacto' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'fecha_evento' => ['nullable', 'date'],
            'hora_acceso' => ['nullable', 'date_format:H:i'],
            'hora_presentacion' => ['nullable', 'date_format:H:i'],
            'tipo_evento' => ['nullable', 'string', Rule::in([
                'privado', 'corporativo', 'publico_masivo', 'social', 'gubernamental',
            ])],
            'con_venta_boletos' => ['nullable', 'boolean'],
            'formato_contratacion' => ['nullable', 'string', 'max:255'],
            'tiene_presupuesto' => ['nullable', 'boolean'],
            'presupuesto_aproximado' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'detalle_actividad' => ['nullable', 'string', 'max:3000'],
            'requerimientos_operacion' => ['nullable', 'string', 'max:3000'],
            'requerimientos_tecnicos' => ['nullable', 'string', 'max:3000'],
            'enviar_cotizacion' => ['nullable', 'boolean'],
        ];
    }
}