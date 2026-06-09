<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'contrasena' => ['required'],
        ]);

        $email = strtolower(trim($request->email));

        /** @var Usuario|null $user */
        $user = Usuario::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $user || ! Hash::check($request->contrasena, (string) $user->contrasena)) {
            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->onlyInput('email');
        }

        if (! $user->activo) {
            return back()->withErrors([
                'email' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return $this->redirectAfterLogin($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function redirectAfterLogin(Usuario $user)
    {
        if ($user->hasAnyRole(['Administrador', 'admin', 'administrador'])) {
            return redirect()->intended('dashboard');
        }

        if ($user->hasAnyRole(['Almacenero', 'almacenero', 'Almacenista', 'almacenista'])) {
            return redirect()->route('almacenes.estructura');
        }

        if ($user->hasAnyRole(['Reportes', 'reportes'])) {
            return redirect()->route('mensajes.index');
        }

        if ($user->hasAnyRole(['Donante', 'donante'])) {
            return redirect()->route('donaciones.index');
        }

        return redirect()->intended('dashboard');
    }
}
