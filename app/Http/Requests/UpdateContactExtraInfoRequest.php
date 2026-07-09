<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactExtraInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa' => ['nullable', 'string', 'max:255'],
            'puesto_contacto' => ['nullable', 'string', 'max:255'],
            'fecha_evento' => ['nullable', 'date'],
            'hora_acceso' => ['nullable', 'date_format:H:i'],
            'hora_presentacion' => ['nullable', 'date_format:H:i'],
            'formato_contratacion' => ['nullable', 'string', 'max:255'],
            'detalle_actividad' => ['nullable', 'string', 'max:3000'],
            'requerimientos_operacion' => ['nullable', 'string', 'max:3000'],
            'requerimientos_tecnicos' => ['nullable', 'string', 'max:3000'],
        ];
    }
}