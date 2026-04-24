<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario; // Importamos el modelo para ayudar al editor

class LoginController extends Controller
{
    /**
     * 1. Mostrar el formulario de Login
     */
    public function showLoginForm()
    {
        // Si el usuario ya está logueado, lo mandamos al dashboard directamente
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * 2. Procesar el Login
     */
    public function login(Request $request)
    {
        // Validamos los datos del formulario
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'contrasena' => ['required'],
        ]);

        // INTENTO DE LOGIN
        // Laravel espera 'password', así que mapeamos tu campo 'contrasena'
        if (Auth::attempt(['email' => $request->email, 'password' => $request->contrasena], $request->filled('remember'))) {
            
            $request->session()->regenerate();

            // Obtenemos el usuario autenticado
            /** @var \App\Models\Usuario $user */ 
            $user = Auth::user(); 
            // ^ El comentario de arriba arregla el error visual "Undefined method hasRole"

            // ----------------------------------------------------
            // LÓGICA DE REDIRECCIÓN POR ROLES (Spatie)
            // ----------------------------------------------------
            
            // 1. Admin -> Dashboard General
            if ($user->hasRole('admin')) {
                return redirect()->intended('dashboard');
            }

            // 2. Almacenero -> Gestión de Almacén
            if ($user->hasRole('almacenero')) {
                return redirect()->route('almacenes.estructura');
            }

            // 3. Reportes -> Bandeja de Mensajes (o Reportes)
            if ($user->hasRole('reportes')) {
                return redirect()->route('mensajes.index');
            }

            // 4. Donante -> Historial de Donaciones
            if ($user->hasRole('donante')) {
                return redirect()->route('donaciones.index');
            }

            // Fallback: Si tiene un rol raro o ninguno, al dashboard por defecto
            return redirect()->intended('dashboard');
        }

        // SI FALLA EL LOGIN (Contraseña incorrecta o email no existe)
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * 3. Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}