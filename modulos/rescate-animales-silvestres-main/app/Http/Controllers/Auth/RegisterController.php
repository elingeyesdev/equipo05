<?php

namespace Modules\Rescate\Http\Controllers\Auth;

use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\User;
use Modules\Rescate\Services\User\UserTrackingService;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        event(new Registered($user));

        Auth::login($user);

        return $this->registered($request, $user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $emailUnique = $this->usesIntegratedCoreAuth()
            ? Rule::unique((new Usuario)->getTable(), 'email')
            : 'unique:users,email';

        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'ci' => ['required', 'string', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', $emailUnique],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'foto' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120', new \Modules\Rescate\Rules\NotWebpImage],
        ];

        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar :max caracteres.',
            'ci.required' => 'El documento (CI) es obligatorio.',
            'ci.max' => 'El CI no puede superar :max caracteres.',
            'telefono.max' => 'El teléfono no puede superar :max caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'foto.required' => 'La foto de perfil es obligatoria.',
            'foto.file' => 'La foto debe ser un archivo válido.',
            'foto.mimes' => 'La foto debe ser una imagen en formato JPG, JPEG o PNG.',
            'foto.max' => 'La foto no puede superar los 5MB.',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \App\Models\Usuario|\Modules\Rescate\Models\User
     */
    protected function create(array $data)
    {
        $fotoPath = null;
        if (isset($data['foto']) && $data['foto']->isValid()) {
            $fotoPath = $data['foto']->store('personas', 'public');
        }

        if ($this->usesIntegratedCoreAuth()) {
            return $this->createIntegratedUsuario($data, $fotoPath);
        }

        return $this->createStandaloneRescateUser($data, $fotoPath);
    }

    /**
     * Flujo original del módulo rescate (solo BD rescate).
     */
    protected function createStandaloneRescateUser(array $data, ?string $fotoPath): User
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $person = Person::create([
            'usuario_id' => $user->id,
            'nombre' => $data['nombre'],
            'ci' => $data['ci'],
            'telefono' => $data['telefono'] ?? null,
            'foto_path' => $fotoPath,
            'es_cuidador' => false,
        ]);

        if (method_exists($user, 'assignRole')) {
            $role = Role::firstOrCreate(['name' => 'ciudadano', 'guard_name' => 'web']);
            $user->assignRole($role);
        }

        try {
            app(UserTrackingService::class)->logUserRegistration($user, [
                'person' => [
                    'id' => $person->id,
                    'nombre' => $person->nombre,
                    'ci' => $person->ci,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::warning('Error registrando tracking de usuario: '.$e->getMessage());
        }

        return $user;
    }

    /**
     * Registro alineado con el login unificado (tabla usuarios + mismos IDs en rescate).
     */
    protected function createIntegratedUsuario(array $data, ?string $fotoPath): Usuario
    {
        $nombreCompleto = trim((string) $data['nombre']);
        $parts = preg_split('/\s+/', $nombreCompleto, 2, PREG_SPLIT_NO_EMPTY);
        $nombre = Str::limit($parts[0] ?? 'Usuario', 50, '');
        $apellido = Str::limit($parts[1] ?? '-', 50, '');

        $imagenUrl = null;
        if ($fotoPath !== null) {
            $imagenUrl = Str::limit('storage/'.$fotoPath, 255, '');
        }

        $usuario = Usuario::create([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $data['email'],
            'contrasena' => Hash::make($data['password']),
            'telefono' => isset($data['telefono']) ? Str::limit((string) $data['telefono'], 20, '') : null,
            'imagenurl' => $imagenUrl,
            'activo' => true,
        ]);

        if (method_exists($usuario, 'assignRole')) {
            $role = Role::firstOrCreate(['name' => 'ciudadano', 'guard_name' => 'web']);
            $usuario->assignRole($role);
        }

        $authId = (int) $usuario->getKey();
        $now = now();

        $rescate = DB::connection('rescate');
        $rescate->table('users')->updateOrInsert(
            ['id' => $authId],
            [
                'email' => $usuario->email,
                'password' => $usuario->contrasena,
                'email_verified_at' => null,
                'remember_token' => null,
                'created_at' => $rescate->table('users')->where('id', $authId)->value('created_at') ?? $now,
                'updated_at' => $now,
            ]
        );

        Person::on('rescate')->updateOrCreate(
            ['usuario_id' => $authId],
            [
                'nombre' => $nombreCompleto,
                'ci' => $data['ci'],
                'telefono' => $data['telefono'] ?? null,
                'foto_path' => $fotoPath,
                'es_cuidador' => false,
            ]
        );

        try {
            $shadow = User::on('rescate')->with('person')->find($authId);
            if ($shadow) {
                app(UserTrackingService::class)->logUserRegistration($shadow, [
                    'person' => [
                        'nombre' => $data['nombre'],
                        'ci' => $data['ci'],
                    ],
                    'integrated' => true,
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Error registrando tracking de usuario: '.$e->getMessage());
        }

        return $usuario;
    }

    /**
     * The user has been registered.
     * Logout immediately and send to login page to start session explicitly.
     * Guardar el reporte pendiente en sesión para asociarlo después del login.
     */
    protected function registered(Request $request, $user)
    {
        // Guardar el reporte pendiente en sesión antes de hacer logout
        // Esto es importante porque el logout puede limpiar la sesión
        $reportId = $request->session()->get('pending_report_id');

        // Hacer logout del usuario recién registrado
        Auth::logout();

        // Restaurar el pending_report_id en la sesión después del logout
        // La sesión web se mantiene aunque el usuario se desautentique
        if ($reportId) {
            $request->session()->put('pending_report_id', $reportId);
        }

        return redirect()->route('login')
            ->with('info', 'Registro exitoso. Por favor inicia sesión para asociar tu reporte.');
    }
}
