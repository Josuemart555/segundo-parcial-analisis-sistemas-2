<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('usuario'))],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', Rule::in(['admin', 'recepcionista', 'doctor'])],
            'especialidad_id' => [Rule::requiredIf(fn () => $this->input('role') === 'doctor'), 'nullable', 'integer', 'exists:especialidades,id'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}
