<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BiomasaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        \Log::info('BiomasaRequest authorize() ejecutándose', [
            'user_id' => auth()->id(),
            'method' => $this->method(),
            'url' => $this->url()
        ]);
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        \Log::info('BiomasaRequest rules() ejecutándose', [
            'all_data' => $this->all()
        ]);
        
        return [
            'fecha_reporte' => ['nullable','date'],
            'tipo_biomasa_id' => ['required','exists:tipo_biomasa,id'],
            'area_m2' => ['nullable','numeric','min:0'],
            'perimetro_m' => ['nullable','numeric','min:0'],
            'densidad' => ['nullable','string'],
            'coordenadas' => ['nullable'],
            'ubicacion' => ['nullable','string'],
            'descripcion' => ['nullable','string','max:1000'],
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que coordenadas sea un JSON válido si está presente
            if ($this->has('coordenadas') && !empty($this->coordenadas)) {
                $coords = is_string($this->coordenadas) ? json_decode($this->coordenadas, true) : $this->coordenadas;
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $validator->errors()->add('coordenadas', 'Las coordenadas deben ser un JSON válido');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // No hacemos nada aquí, dejamos que la validación maneje el JSON como string
    }
}
