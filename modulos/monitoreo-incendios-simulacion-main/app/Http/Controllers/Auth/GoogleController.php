<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Voluntario;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

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
                return redirect('/');
            }

            // Check if email already exists
            $emailUser = User::where('email', $user->getEmail())->first();

            if ($emailUser) {
                // Link Google ID to existing user
                $emailUser->update(['google_id' => $user->getId()]);
                Auth::login($emailUser, true);
                return redirect('/');
            }

            // Create new user
            $newUser = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'google_id' => $user->getId(),
                'password' => bcrypt('google_oauth_' . uniqid()), // Random password since it's OAuth
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

            return redirect('/');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Error al autenticarse con Google: ' . $e->getMessage());
        }
    }
}
