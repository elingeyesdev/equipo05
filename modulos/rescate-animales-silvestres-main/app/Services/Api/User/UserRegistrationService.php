<?php

namespace Modules\Rescate\Services\Api\User;

use App\Support\UnifiedPostgres;
use App\Support\UnifiedValidation;
use Modules\Rescate\Models\User;
use Modules\Rescate\Models\Person;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Modules\Rescate\Services\User\UserTrackingService;

class UserRegistrationService
{
    /**
     * Registra un usuario y su persona asociada en una transacción.
     *
     * @param  array  $data
     * @return array{user: User, person: Person}
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $userPayload = [
                'email'    => $data['email'],
                'password' => $data['password'],
            ];

            if (UnifiedPostgres::enabled()) {
                $names = UnifiedValidation::splitNombreCompleto($data['nombre']);
                $userPayload['nombre'] = $names['nombre'];
                $userPayload['apellido'] = $names['apellido'];
            }

            $user = User::create($userPayload);

            $person = Person::create([
                'usuario_id' => $user->id,
                'nombre'     => $data['nombre'],
                'ci'         => $data['ci'],
                'telefono'   => $data['telefono'],
                'es_cuidador'=> $data['es_cuidador'] ?? false,
            ]);

            // Rol por defecto: ciudadano (se asegura que exista aunque el seeder no se haya ejecutado)
            if (method_exists($user, 'assignRole')) {
                $role = Role::firstOrCreate(['name' => 'ciudadano', 'guard_name' => 'web']);
                $user->assignRole($role);
            }

            // Registrar tracking de registro
            try {
                app(UserTrackingService::class)->logUserRegistration($user, [
                    'person' => [
                        'id' => $person->id,
                        'nombre' => $person->nombre,
                        'ci' => $person->ci,
                    ],
                ]);
            } catch (\Exception $e) {
                // No fallar el registro si el tracking falla
                \Log::warning('Error registrando tracking de usuario (API): ' . $e->getMessage());
            }

            return [
                'user'   => $user,
                'person' => $person,
            ];
        });
    }
}
