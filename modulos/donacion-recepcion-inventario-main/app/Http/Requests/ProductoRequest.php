<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Inventario\Models\CategoriasProducto;
use Modules\Inventario\Models\Producto;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('producto');
        if ($id instanceof Producto) {
            $id = $id->id_producto;
        }

        return [
            'id_categoria' => 'required|integer|exists:inventario.categorias_productos,id_categoria',
            'codigo' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^PROD-[A-Z0-9-]+$/',
                Rule::unique('inventario.productos', 'codigo')->ignore($id, 'id_producto'),
            ],
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'imagen_url' => 'nullable|string|max:500',
            'unidad_medida' => 'required|string|max:50',
            'prioridad' => ['required', 'string', Rule::in(array_keys(Producto::PRIORIDADES))],
            'estado' => ['required', 'string', Rule::in(array_keys(Producto::ESTADOS))],
            'requiere_vencimiento' => 'sometimes|boolean',
            'requiere_talla' => 'sometimes|boolean',
            'requiere_condicion' => 'sometimes|boolean',
            'producto_restringido' => 'sometimes|boolean',
            'stock_minimo' => 'nullable|integer|min:0',
            'condiciones_almacenamiento' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'Ya existe un producto con ese código.',
            'codigo.regex' => 'El código debe tener el formato PROD-AGUA-001.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $codigo = $this->codigo ? strtoupper(trim($this->codigo)) : null;
        if (! $codigo && $this->nombre) {
            $codigo = Producto::generarCodigo($this->nombre);
        }

        $this->merge([
            'codigo' => $codigo,
            'prioridad' => $this->prioridad ?: 'media',
            'estado' => $this->estado ?: 'activo',
            'stock_minimo' => $this->stock_minimo ?? 0,
            'requiere_vencimiento' => $this->boolean('requiere_vencimiento'),
            'requiere_talla' => $this->boolean('requiere_talla'),
            'requiere_condicion' => $this->boolean('requiere_condicion'),
            'producto_restringido' => $this->boolean('producto_restringido'),
        ]);
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }
        parent::failedValidation($validator);
    }
}
