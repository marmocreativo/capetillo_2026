<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isContratacion = $this->input('type') === 'contratacion';

        return [
            'type' => ['required', Rule::in(['general', 'contratacion'])],
            'talent_id' => ['nullable', 'exists:talents,id'],
            'talent_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
            'estado_republica' => [$isContratacion ? 'required' : 'nullable', 'string', 'max:255'],
            'fecha_evento' => ['nullable', 'date', 'after_or_equal:today'],
            'aforo_esperado' => ['nullable', 'integer', 'min:1'],
            'venue' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'tipo_evento' => ['nullable', 'string', Rule::in([
                'privado', 'corporativo', 'publico_masivo', 'social', 'gubernamental',
            ])],
            'con_venta_boletos' => ['nullable', 'boolean'],
            'tiene_presupuesto' => ['nullable', 'boolean'],
            'presupuesto_aproximado' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }
}