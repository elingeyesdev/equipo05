<?php

namespace Modules\Rescate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);
        $rules = [
            // persona_id and aprobado are set server-side (aprobado solo en update)
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096', new \Modules\Rescate\Rules\NotWebpImage()],
            'observaciones' => 'nullable|string',
            // ubicación solo se exige en creación
            'latitud' => $isUpdate ? 'nullable|numeric' : 'required|numeric',
            'longitud' => $isUpdate ? 'nullable|numeric' : 'required|numeric',
            'direccion' => 'nullable|string',
            // nuevos campos parametrizables
            'condicion_inicial_id' => 'required|exists:rescate.animal_conditions,id',
            'tipo_incidente_id' => 'required|exists:rescate.incident_types,id',
            'tamano' => 'required|in:pequeno,mediano,grande',
            'puede_moverse' => 'required|boolean',
            // traslado_inmediato y centro solo aplican al crear
            'traslado_inmediato' => $isUpdate ? 'nullable' : 'nullable|boolean',
            'centro_id' => $isUpdate ? 'nullable' : 'nullable|exists:rescate.centers,id|required_if:traslado_inmediato,1',
            // incendio_id para endpoints externos (API Gateway)
            'incendio_id' => 'nullable|integer',
            'especie_id' => 'nullable|integer|exists:rescate.species,id',
        ];

        if ($isUpdate) {
            $rules['aprobado'] = 'required|boolean';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'imagen.image' => 'La fotografía debe ser una imagen válida (JPG o PNG).',
            'imagen.mimes' => 'Solo se permiten archivos JPG o PNG.',
            'imagen.max' => 'La imagen no puede superar 4 MB.',
            'latitud.required' => 'Debes marcar la ubicación en el mapa.',
            'longitud.required' => 'Debes marcar la ubicación en el mapa.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            try {
                $condDesconocidoId = \Modules\Rescate\Models\AnimalCondition::where('nombre', 'Desconocido')->value('id');
                $incOtroId = \Modules\Rescate\Models\IncidentType::where('nombre', 'Otro')->value('id');
            } catch (\Throwable $e) {
                $condDesconocidoId = null;
                $incOtroId = null;
            }
            $condId = $this->input('condicion_inicial_id');
            $incId = $this->input('tipo_incidente_id');
            $obs = trim((string)$this->input('observaciones', ''));
            $mustExplain = ($condDesconocidoId && (string)$condId === (string)$condDesconocidoId)
                || ($incOtroId && (string)$incId === (string)$incOtroId);
            if ($mustExplain && $obs === '') {
                $v->errors()->add('observaciones', 'Debe especificar detalles en Observaciones para el caso seleccionado.');
            }
        });
    }
}
