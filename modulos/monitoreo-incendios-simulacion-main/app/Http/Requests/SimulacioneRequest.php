<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimulacioneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:255'],
            'fecha' => ['nullable','date'],
            'duracion' => ['nullable','integer','min:0'],
            'focos_activos' => ['nullable','integer','min:0'],
            'num_voluntarios_enviados' => ['nullable','integer','min:0'],
            'estado' => ['nullable','in:pendiente,en_progreso,completada'],
        ];
    }
}
