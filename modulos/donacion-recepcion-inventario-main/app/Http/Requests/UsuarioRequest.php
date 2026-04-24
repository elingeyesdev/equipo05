<?php

namespace Modules\Inventario\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
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
        $usuario = $this->route('inventario.usuario');
        $usuarioId = $usuario ? $usuario->id_usuario : null;

        $rules = [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:150',
            'ci' => 'required|string|max:20|unique:inventario.usuarios,ci' . ($usuarioId ? ',' . $usuarioId . ',id_usuario' : ''),
            'foto_ci' => 'nullable|string',
            'licencia_conducir' => $this->input('is_recolector') ? 'required|string|max:50' : 'nullable|string|max:50',
            'foto_licencia' => 'nullable|string',
            'genero' => 'nullable|string|in:Masculino,Femenino,Otro',
            'correo' => 'required|email|max:100|unique:inventario.usuarios,correo' . ($usuarioId ? ',' . $usuarioId . ',id_usuario' : ''),
            'telefono' => 'nullable|string|max:20',
            'direccion_domicilio' => 'nullable|string',
            'estado' => 'nullable|string|in:Activo,Inactivo',
            'entidad_pertenencia' => 'nullable|string|max:150',
            'tipo_sangre' => 'nullable|string|max:5',
            'rol' => 'nullable|string|exists:inventario.roles,name',
            'is_recolector' => 'nullable|boolean',
        ];

        // Contraseña requerida solo al crear, opcional al actualizar
        if ($this->isMethod('post')) {
            $rules['contrasena'] = 'required|string|min:6';
        } else {
            $rules['contrasena'] = 'nullable|string|min:6';
        }

        return $rules;
    }
}






