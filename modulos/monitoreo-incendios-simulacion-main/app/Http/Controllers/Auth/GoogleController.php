<?php

namespace Modules\Incendios\Http\Controllers\Auth;

use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Models\User;
use Modules\Incendios\Models\Voluntario;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            // Check if user already exists
            $existingUser = User::where('google_id', $user->getId())->first();

            if ($existingUser) {
                // Login existing user
                Auth::login($existingUser, true);

                return redirect()->route('incendios.dashboard');
            }

            // Check if email already exists
            $emailUser = User::where('email', $user->getEmail())->first();

            if ($emailUser) {
                // Link Google ID to existing user
                $emailUser->update(['google_id' => $user->getId()]);
                Auth::login($emailUser, true);

                return redirect()->route('incendios.dashboard');
            }

            // Create new user
            $newUser = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'google_id' => $user->getId(),
                'password' => bcrypt('google_oauth_'.uniqid()), // Random password since it's OAuth
            ]);

            // Create Voluntario profile (default role)
            Voluntario::create([
                'user_id' => $newUser->id,
                'direccion' => 'Por definir',
                'ciudad' => 'Por definir',
                'zona' => 'Por definir',
                'disponibilidad' => true,
            ]);

            // Assign voluntario role using Spatie
            $newUser->assignRole('voluntario');

            // Login new user
            Auth::login($newUser, true);

            return redirect()->route('incendios.dashboard');

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Error al autenticarse con Google: '.$e->getMessage());
        }
    }
}
