<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => ['sometimes', 'integer', 'exists:pacientes,id'],
            'doctor_id' => ['sometimes', 'integer', 'exists:doctores,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
            'motivo' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
