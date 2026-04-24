<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaqueteRequest extends FormRequest
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
            'codigo_paquete' => 'nullable|string',
            'id_solicitud' => 'nullable|integer',
            'estado' => 'nullable|string',
            'codigo_solicitud_externa' => 'nullable|string',
            'paquete_externo_id' => 'nullable|integer',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'detalles.*.cantidad_usada' => 'required|integer|min:1',
        ];

        // Solo requerir id_paquete cuando se está actualizando
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['id_paquete'] = 'required';
        }

        return $rules;
    }
}





