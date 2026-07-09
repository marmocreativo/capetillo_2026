<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageTalentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'talent_ids' => ['required', 'array', 'min:1'],
            'talent_ids.*' => ['exists:talents,id'],
        ];
    }
}