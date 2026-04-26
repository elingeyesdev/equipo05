<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoBiomasaRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $tipoBiomasaId = $this->route('tipo_biomasa') ? $this->route('tipo_biomasa')->id : null;
        
        return [
            'tipo_biomasa' => 'required|string|max:255|unique:tipo_biomasa,tipo_biomasa,' . $tipoBiomasaId,
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'modificador_intensidad' => ['required', 'numeric', 'min:0.5', 'max:2.0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'tipo_biomasa' => 'tipo de biomasa',
            'color' => 'color de identificación',
            'modificador_intensidad' => 'modificador de intensidad',
        ];
    }
}
