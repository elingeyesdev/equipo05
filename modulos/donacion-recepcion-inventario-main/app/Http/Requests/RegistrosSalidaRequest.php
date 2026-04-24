<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrosSalidaRequest extends FormRequest
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
        $rules = [
            'id_paquete' => 'nullable|integer|exists:inventario.paquetes,id_paquete',
            'fecha_salida' => 'nullable|date',
            'destino' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ];
        
        // Solo requerir id_salida cuando se está actualizando
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['id_salida'] = 'required';
        }
        
        return $rules;
    }
}






