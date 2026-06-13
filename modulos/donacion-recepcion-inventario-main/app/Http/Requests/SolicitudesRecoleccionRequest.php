<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolicitudesRecoleccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_donante' => 'required|integer|exists:inventario.donantes,id_donante',
            'id_recolector' => [
                'required',
                'integer',
                Rule::exists('inventario.usuarios', 'id_usuario')->where(function ($query) {
                    $query->where('is_recolector', true);
                }),
            ],
            'direccion_recoleccion' => 'required|string',
            'fecha_programada' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado' => 'nullable|string|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'id_recolector.required' => 'Debe seleccionar un recolector activo.',
            'id_recolector.exists' => 'El recolector seleccionado no es válido o no está habilitado.',
            'fecha_programada.required' => 'Debe asignar una fecha programada.',
        ];
    }
}
