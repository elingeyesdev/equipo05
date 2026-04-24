<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonacioneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'id_donante' => 'required|exists:donantes,id_donante',
            'tipo' => 'required|in:dinero,especie,ropa',
            'id_campana' => 'nullable|exists:campanas,id_campana',
            'id_punto_recoleccion' => 'nullable|exists:puntos_recoleccion,id_punto',
            'observaciones' => 'nullable|string',
        ];

        // Validaciones específicas para tipo dinero
        if ($this->input('tipo') === 'dinero') {
            $rules['monto'] = 'required|numeric|min:0.01';
            $rules['moneda'] = 'nullable|string|max:10';
            $rules['metodo_pago'] = 'nullable|string|max:50';
            $rules['referencia_pago_file'] = 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:5120';
        }

        // Validaciones específicas para tipo especie/ropa
        if (in_array($this->input('tipo'), ['especie', 'ropa'])) {
            $rules['detalles'] = 'required|array|min:1';
            $rules['detalles.*.id_producto'] = 'required|exists:productos,id_producto';
            $rules['detalles.*.cantidad'] = 'required|numeric|min:1';
            $rules['detalles.*.unidad_medida'] = 'nullable|string|max:50';
            $rules['detalles.*.id_talla'] = 'nullable|exists:tallas,id_talla';
            $rules['detalles.*.id_genero'] = 'nullable|exists:generos_ropa,id_genero';
            $rules['detalles.*.id_espacio'] = 'nullable|exists:espacios,id_espacio';
        }

        return $rules;
    }
}





