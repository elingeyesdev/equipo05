<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Inventario\Models\CategoriasProducto;

class CategoriasProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('GET')) {
            return true;
        }

        return $this->user()?->hasRole('Administrador') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('categorias_producto');
        if ($id instanceof CategoriasProducto) {
            $id = $id->id_categoria;
        }

        return [
            'nombre' => [
                'required',
                'string',
                'max:120',
                Rule::unique('inventario.categorias_productos', 'nombre')->ignore($id, 'id_categoria'),
            ],
            'codigo' => [
                'nullable',
                'string',
                'max:24',
                'regex:/^CAT-[A-Z0-9-]+$/',
                Rule::unique('inventario.categorias_productos', 'codigo')->ignore($id, 'id_categoria'),
            ],
            'descripcion' => 'nullable|string|max:2000',
            'tipo_categoria' => ['required', 'string', Rule::in(array_keys(CategoriasProducto::TIPOS_CATEGORIA))],
            'unidad_medida' => ['nullable', 'string', Rule::in(array_keys(CategoriasProducto::UNIDADES_MEDIDA))],
            'es_perecedero' => 'sometimes|boolean',
            'requiere_fecha_vencimiento' => 'sometimes|boolean',
            'prioridad' => ['required', 'string', Rule::in(array_keys(CategoriasProducto::PRIORIDADES))],
            'condiciones_almacenamiento' => 'nullable|string|max:2000',
            'recomendaciones_uso' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
            'codigo.unique' => 'Ya existe una categoría con ese código.',
            'codigo.regex' => 'El código debe tener el formato CAT-AGUA, CAT-ALIM-PER, etc.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $codigo = $this->codigo ? strtoupper(trim($this->codigo)) : null;
        if (! $codigo && $this->nombre) {
            $codigo = CategoriasProducto::generarCodigo($this->nombre);
        }

        $this->merge([
            'codigo' => $codigo,
            'tipo_categoria' => $this->tipo_categoria ?: 'OTRO',
            'prioridad' => $this->prioridad ?: 'media',
            'es_perecedero' => $this->boolean('es_perecedero'),
            'requiere_fecha_vencimiento' => $this->boolean('requiere_fecha_vencimiento'),
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

    protected function failedAuthorization()
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Solo administradores pueden gestionar categorías.',
                ], 403)
            );
        }

        abort(403, 'Solo administradores pueden gestionar categorías.');
    }
}
