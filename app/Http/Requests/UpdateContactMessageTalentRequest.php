<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactMessageTalentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['nullable', 'string', 'max:255'],
            'honorarios' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'incluye' => ['nullable', 'string', 'max:2000'],
            'condiciones_pago' => ['nullable', 'string', 'max:2000'],
        ];
    }
}