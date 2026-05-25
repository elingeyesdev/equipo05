<?php

namespace Modules\Incendios\Http\Requests;

use App\Support\UnifiedPostgres;
use App\Support\UnifiedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : (is_numeric($user) ? (int) $user : null);

        $emailRule = Rule::unique(UnifiedValidation::incendiosUsersTable(), 'email');
        if ($userId) {
            $emailRule->ignore($userId, UnifiedValidation::incendiosUsersKey());
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'telefono' => ['required', 'string', 'max:30'],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string', 'min:8', 'confirmed',
            ],
        ];

        if (! UnifiedPostgres::enabled()) {
            $cedulaRule = Rule::unique('users', 'cedula_identidad');
            if ($userId) {
                $cedulaRule->ignore($userId);
            }
            $rules['cedula_identidad'] = ['required', 'string', 'max:50', $cedulaRule];
        }

        return $rules;
    }
}
