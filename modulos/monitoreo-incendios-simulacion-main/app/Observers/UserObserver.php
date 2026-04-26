<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Voluntario;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Ensures every new user has the 'voluntario' role assigned.
     */
    public function created(User $user): void
    {
        // Check if user has no roles assigned
        if ($user->roles()->count() === 0) {
            // Assign voluntario role by default
            $user->assignRole('voluntario');
            
            // Create Voluntario profile if it doesn't exist
            if (!$user->voluntario) {
                Voluntario::create([
                    'user_id' => $user->id,
                    'direccion' => 'Por definir',
                    'ciudad' => 'Por definir',
                    'zona' => 'Por definir',
                    'notas' => 'Perfil creado automáticamente',
                ]);
            }
            
            Log::info("Usuario {$user->email} creado automáticamente como voluntario");
        }
    }
}

