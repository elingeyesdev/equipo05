<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : (is_numeric($user) ? (int)$user : null);

        return [
            'name' => ['required','string','max:255'],
            'email' => [
                'required','email','max:255',
                $userId ? Rule::unique('users','email')->ignore($userId) : Rule::unique('users','email'),
            ],
            'telefono' => ['required','string','max:30'],
            'cedula_identidad' => [
                'required','string','max:50',
                $userId ? Rule::unique('users','cedula_identidad')->ignore($userId) : Rule::unique('users','cedula_identidad'),
            ],
            // On create: required; on update: optional. Always must be confirmed if provided.
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string','min:8','confirmed'
            ],
        ];
    }
}
