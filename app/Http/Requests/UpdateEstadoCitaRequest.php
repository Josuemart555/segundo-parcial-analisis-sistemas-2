<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('estados_cita', 'slug')->ignore($this->route('estados_cita'))],
            'nombre' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'es_terminal' => ['sometimes', 'boolean'],
            'bloquea_horario' => ['sometimes', 'boolean'],
            'orden' => ['required', 'integer', 'min:0'],
        ];
    }
}
