<?php

namespace Modules\Incendios\Http\Requests;

use App\Support\UnifiedValidation;
use Illuminate\Foundation\Http\FormRequest;

class PredictionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'foco_incendio_id' => 'required|exists:'.UnifiedValidation::incendiosTable('focos_incendios').',id',
            'predicted_at' => 'nullable|date',
            'path' => 'nullable|json',
            'meta' => 'nullable|json',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'foco_incendio_id' => 'foco de incendio',
            'predicted_at' => 'fecha de predicción',
            'path' => 'ruta',
            'meta' => 'metadatos',
        ];
    }
}
