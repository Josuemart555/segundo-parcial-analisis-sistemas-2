<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEspecialidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', Rule::unique('especialidades', 'nombre')->ignore($this->route('especialidad'))],
            'activa' => ['sometimes', 'boolean'],
        ];
    }
}
