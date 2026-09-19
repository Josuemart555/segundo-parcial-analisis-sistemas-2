<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:estados_cita,slug'],
            'nombre' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'es_terminal' => ['sometimes', 'boolean'],
            'bloquea_horario' => ['sometimes', 'boolean'],
            'orden' => ['required', 'integer', 'min:0'],
        ];
    }
}
