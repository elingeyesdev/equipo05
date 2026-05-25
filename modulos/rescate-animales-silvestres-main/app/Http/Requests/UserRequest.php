<?php

namespace Modules\Rescate\Http\Requests;

use App\Support\UnifiedPostgres;
use App\Support\UnifiedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Rescate\Models\User;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->getKey() : null;
        $isCreating = $this->isMethod('post');

        $emailTable = UnifiedPostgres::enabled() ? UnifiedValidation::coreUsuariosTable() : 'rescate.users';
        $emailKey = UnifiedPostgres::enabled() ? UnifiedValidation::coreUsuariosKey() : 'id';

        $emailRule = Rule::unique($emailTable, 'email');
        if ($userId) {
            $emailRule->ignore($userId, $emailKey);
        }

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
        ];

        if ($isCreating) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }
}
