<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FocosIncendioRequest extends FormRequest
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
            'fecha' => ['required','date'],
            'ubicacion' => ['required','string','max:255'],
            'coordenadas' => ['required','array','size:2'],
            'coordenadas.*' => ['required','numeric'],
            'intensidad' => ['required','numeric','min:0','max:10'],
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        // Ya no necesitamos validación extra porque ahora validamos directamente el array
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convertir coordenadas de string JSON a array antes de validar
        if ($this->has('coordenadas') && is_string($this->coordenadas)) {
            $decoded = json_decode($this->coordenadas, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge([
                    'coordenadas' => $decoded
                ]);
            }
        }
    }
}
