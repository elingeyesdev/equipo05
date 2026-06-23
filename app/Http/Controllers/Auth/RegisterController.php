<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Support\AccessControl;
use App\Support\OwnershipScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::check()) {
            /** @var Usuario $user */
            $user = Auth::user();

            return redirect(AccessControl::redirectPathFor($user));
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique((new Usuario)->getTable(), 'email'),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Este correo ya está registrado. Inicia sesión con tu cuenta.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = Usuario::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => strtolower(trim($data['email'])),
            'telefono' => $data['telefono'] ?? null,
            'contrasena' => Hash::make($data['contrasena']),
            'activo' => true,
            'fecharegistro' => now(),
        ]);

        AccessControl::syncPublicCommunityRoles($user);

        try {
            OwnershipScope::ensureInventarioDonanteProfile($user);
        } catch (\Throwable) {
            // Inventario no disponible en este entorno; la cuenta igual queda operativa.
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->to(AccessControl::redirectPathFor($user))
            ->with('success', 'Cuenta creada. Ya puedes reportar incendios y registrar donaciones.');
    }
}
