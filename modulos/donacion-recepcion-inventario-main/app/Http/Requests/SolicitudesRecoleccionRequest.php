<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudesRecoleccionRequest extends FormRequest
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
			'id_donante' => 'required|integer|exists:donantes,id_donante',
			'id_recolector' => [
                'nullable',
                'integer',
                'exists:usuarios,id_usuario',
                'required_if:estado,en_proceso',
            ],
			'direccion_recoleccion' => 'required|string',
			'fecha_programada' => [
                'nullable',
                'date',
                'required_if:estado,en_proceso',
            ],
			'observaciones' => 'nullable|string',
			'estado' => 'nullable|string|max:30',
        ];
    }
    
    public function messages(): array
    {
        return [
            'id_recolector.required_if' => 'Debe asignar un recolector para cambiar el estado a "En Proceso".',
            'fecha_programada.required_if' => 'Debe asignar una fecha programada para cambiar el estado a "En Proceso".',
        ];
    }
}





