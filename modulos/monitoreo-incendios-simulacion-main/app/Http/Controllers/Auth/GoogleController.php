<?php

namespace Modules\Incendios\Http\Controllers\Auth;

use App\Models\Usuario;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Models\User;
use Modules\Incendios\Models\Voluntario;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    /**
     * Cuando el guard usa {@see Usuario}, la sesión debe ser del core y la fila en incendios es sombra (mismo ID).
     */
    protected function usesIntegratedCoreAuth(): bool
    {
        $model = config('auth.providers.users.model');

        return is_string($model) && $model === Usuario::class;
    }

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
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if ($this->usesIntegratedCoreAuth()) {
                return $this->handleIntegratedGoogleCallback($googleUser);
            }

            // --- Flujo original del módulo (solo BD incendios) ---
            $existingUser = User::where('google_id', $googleUser->getId())->first();

            if ($existingUser) {
                Auth::login($existingUser, true);

                return redirect()->route('incendios.dashboard');
            }

            $emailUser = User::where('email', $googleUser->getEmail())->first();

            if ($emailUser) {
                $emailUser->update(['google_id' => $googleUser->getId()]);
                Auth::login($emailUser, true);

                return redirect()->route('incendios.dashboard');
            }

            $newUser = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt('google_oauth_'.uniqid()), // Random password since it's OAuth
            ]);

            Voluntario::create([
                'user_id' => $newUser->id,
                'direccion' => 'Por definir',
                'ciudad' => 'Por definir',
                'zona' => 'Por definir',
                'disponibilidad' => true,
            ]);

            $newUser->assignRole('voluntario');

            Auth::login($newUser, true);

            return redirect()->route('incendios.dashboard');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'No se pudo completar la autenticación con Google. Inténtalo nuevamente.');
        }
    }

    /**
     * OAuth Google alineado con usuario central y sombra en conexión incendios.
     */
    protected function handleIntegratedGoogleCallback(object $googleUser): RedirectResponse
    {
        $googleId = $googleUser->getId();
        $emailRaw = $googleUser->getEmail();
        if ($emailRaw === null || $emailRaw === '') {
            return redirect()->route('login')->with('error', 'Google no devolvió un correo electrónico válido.');
        }

        $email = Str::limit((string) $emailRaw, 100, '');
        $fullName = trim((string) ($googleUser->getName() ?: 'Usuario'));

        $incRow = DB::connection('incendios')->table('users')->where('google_id', $googleId)->first();
        if ($incRow) {
            $core = Usuario::query()->whereKey($incRow->id)->first();
            if ($core instanceof Authenticatable) {
                Auth::login($core, true);

                return redirect()->route('incendios.dashboard');
            }
        }

        $core = Usuario::query()->where('email', $email)->first();
        if ($core instanceof Authenticatable) {
            $this->upsertIncendiosShadowUser($core, $googleId, $fullName, $email);
            $this->ensureVoluntarioProfile((int) $core->getKey());
            Auth::login($core, true);

            return redirect()->route('incendios.dashboard');
        }

        $parts = preg_split('/\s+/', $fullName, 2, PREG_SPLIT_NO_EMPTY);
        $nombre = Str::limit($parts[0] ?? 'Usuario', 50, '');
        $apellido = Str::limit($parts[1] ?? '-', 50, '');

        $core = Usuario::create([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'contrasena' => Hash::make('google_oauth_'.Str::random(32)),
            'activo' => true,
        ]);

        if (method_exists($core, 'assignRole')) {
            Role::firstOrCreate(['name' => 'Voluntario', 'guard_name' => 'web']);
            $core->assignRole('Voluntario');
        }

        $this->upsertIncendiosShadowUser($core, $googleId, $fullName, $email);
        $this->ensureVoluntarioProfile((int) $core->getKey());

        Auth::login($core, true);

        return redirect()->route('incendios.dashboard');
    }

    protected function upsertIncendiosShadowUser(Usuario $core, string $googleId, string $displayName, string $email): void
    {
        $id = (int) $core->getKey();
        $now = now();
        $conn = DB::connection('incendios');

        $conn->table('users')->updateOrInsert(
            ['id' => $id],
            [
                'name' => Str::limit($displayName, 255, ''),
                'email' => $email,
                'password' => $core->contrasena,
                'google_id' => $googleId,
                'email_verified_at' => $now,
                'remember_token' => null,
                'updated_at' => $now,
                'created_at' => $conn->table('users')->where('id', $id)->value('created_at') ?? $now,
            ]
        );
    }

    protected function ensureVoluntarioProfile(int $userId): void
    {
        Voluntario::on('incendios')->firstOrCreate(
            ['user_id' => $userId],
            [
                'direccion' => 'Por definir',
                'ciudad' => 'Por definir',
                'zona' => 'Por definir',
            ]
        );
    }
}
