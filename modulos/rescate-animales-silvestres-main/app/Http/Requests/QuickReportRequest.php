<?php

namespace Modules\Rescate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuickReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_emergencia' => 'required|in:incendio,otro',
            'descripcion' => 'nullable|string|max:5000',
            'nombre' => 'nullable|string|max:190',
            'telefono' => 'nullable|string|max:60',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', new \Modules\Rescate\Rules\NotWebpImage],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $tipo = $this->input('tipo_emergencia');
            $desc = trim((string) $this->input('descripcion', ''));
            if ($tipo === 'otro' && $desc === '') {
                $v->errors()->add('descripcion', 'Describa la emergencia cuando el tipo es «Otra».');
            }
        });
    }
}
