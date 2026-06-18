<?php

namespace Database\Seeders;

use App\Services\Auth\CoreUserProvisioningService;
use App\Support\AccessControl;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Inventario\Models\Usuario as InventarioUsuario;

/**
 * Pobla inventario.usuarios con personal operativo alineado al login central y al módulo.
 */
class InventarioUsuariosOperativosSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('inventario')->hasTable('usuarios')) {
            $this->command?->warn('Inventario: tabla usuarios no disponible.');

            return;
        }

        $this->ensureInventarioRoles();

        $personal = $this->personalOperativo();
        $creados = 0;
        $provisioning = app(CoreUserProvisioningService::class);

        foreach ($personal as $item) {
            $email = strtolower($item['correo']);
            $existente = InventarioUsuario::query()->whereRaw('LOWER(correo) = ?', [$email])->first();

            if ($existente) {
                $this->assignInventarioRole((int) $existente->id_usuario, $item['rol']);
                if ($item['is_recolector'] ?? false) {
                    $existente->update(['is_recolector' => true]);
                }
                $provisioning->syncFromInventario($existente->fresh(), $item['rol']);

                continue;
            }

            $password = $this->resolvePassword($email, $item['password'] ?? null);

            $usuario = InventarioUsuario::create([
                'nombres' => $item['nombres'],
                'apellidos' => $item['apellidos'],
                'ci' => $item['ci'],
                'licencia_conducir' => $item['licencia_conducir'] ?? null,
                'genero' => $item['genero'] ?? 'Masculino',
                'correo' => $email,
                'telefono' => $item['telefono'],
                'direccion_domicilio' => $item['direccion'],
                'contrasena' => $password,
                'estado' => $item['estado'] ?? 'Activo',
                'entidad_pertenencia' => $item['entidad'] ?? 'Gobierno Departamental Santa Cruz — Emergencias',
                'tipo_sangre' => $item['tipo_sangre'] ?? null,
                'is_recolector' => (bool) ($item['is_recolector'] ?? false),
                'fecha_registro' => Carbon::parse($item['fecha_registro'] ?? now()->subDays(rand(30, 400))),
            ]);

            $this->assignInventarioRole((int) $usuario->id_usuario, $item['rol']);
            $provisioning->syncFromInventario($usuario->fresh(), $item['rol']);
            $creados++;
        }

        $this->vincularRecolectoresSolicitudes();

        $this->command?->info("Inventario: {$creados} usuarios operativos sembrados (total: ".InventarioUsuario::count().').');
    }

    private function ensureInventarioRoles(): void
    {
        foreach (AccessControl::FINAL_ROLES as $roleName) {
            DB::connection('inventario')->table('roles')->updateOrInsert(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    private function assignInventarioRole(int $usuarioId, string $roleName): void
    {
        $roleId = DB::connection('inventario')
            ->table('roles')
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->value('id');

        if (! $roleId) {
            return;
        }

        DB::connection('inventario')
            ->table('model_has_roles')
            ->where('model_type', InventarioUsuario::class)
            ->where('model_id', $usuarioId)
            ->delete();

        DB::connection('inventario')->table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => InventarioUsuario::class,
            'model_id' => $usuarioId,
        ]);
    }

    private function resolvePassword(string $email, ?string $plain): string
    {
        $plain ??= explode('@', $email)[0];

        $core = DB::connection('core')
            ->table('usuarios')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->value('contrasena');

        if (is_string($core) && $core !== '' && app(CoreUserProvisioningService::class)->isBcryptHash($core)) {
            return $core;
        }

        return Hash::make($plain);
    }

    private function vincularRecolectoresSolicitudes(): void
    {
        if (! Schema::connection('inventario')->hasTable('solicitudes_recoleccion')) {
            return;
        }

        $db = DB::connection('inventario');
        $recolectores = InventarioUsuario::recolectoresActivos()->pluck('id_usuario');

        if ($recolectores->isEmpty()) {
            return;
        }

        $pendientes = $db->table('solicitudes_recoleccion')
            ->where('estado', 'en_camino')
            ->whereNull('id_recolector')
            ->orderBy('id_solicitud')
            ->get();

        foreach ($pendientes as $i => $sol) {
            $db->table('solicitudes_recoleccion')
                ->where('id_solicitud', $sol->id_solicitud)
                ->update(['id_recolector' => $recolectores[$i % $recolectores->count()]]);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function personalOperativo(): array
    {
        return [
            [
                'nombres' => 'Pedro', 'apellidos' => 'Almacen', 'ci' => '5845210',
                'correo' => 'almacen123@gmail.com', 'telefono' => '770012345',
                'direccion' => 'Av. Cristo Redentor, Plan 3000, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Almacenero', 'fecha_registro' => '2025-11-15',
            ],
            [
                'nombres' => 'Super', 'apellidos' => 'Admin', 'ci' => '1000001',
                'correo' => 'admin123@gmail.com', 'telefono' => '770000001',
                'direccion' => 'Equipetrol, Av. San Martín s/n, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Administrador', 'fecha_registro' => '2025-10-01',
            ],
            [
                'nombres' => 'Ana', 'apellidos' => 'Supervisora', 'ci' => '6123456',
                'correo' => 'ana.supervisora.almacen@emergencias.scz.bo', 'telefono' => '771234567',
                'direccion' => 'Depósito Norte, Av. Banzer km 8, Santa Cruz',
                'genero' => 'Femenino', 'rol' => 'Almacenero', 'fecha_registro' => '2026-01-20',
            ],
            [
                'nombres' => 'Roberto', 'apellidos' => 'Mamani', 'ci' => '7891234',
                'correo' => 'roberto.mamani.almacen@emergencias.scz.bo', 'telefono' => '772345678',
                'direccion' => 'Bodega Cotoca, zona industrial, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Almacenero', 'fecha_registro' => '2026-02-10',
            ],
            [
                'nombres' => 'Luis', 'apellidos' => 'Logistica', 'ci' => '6234789',
                'correo' => 'logistica123@gmail.com', 'telefono' => '773456789',
                'direccion' => 'Centro logístico, Doble vía La Guardia, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Coordinador Logístico', 'fecha_registro' => '2025-12-05',
            ],
            [
                'nombres' => 'Juan', 'apellidos' => 'Perez', 'ci' => '7012345',
                'correo' => 'juan1232@gmail.com', 'telefono' => '774567890',
                'direccion' => 'Barrio Hamacas, Av. Roca y Coronado, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Donante', 'fecha_registro' => '2026-03-01',
            ],
            [
                'nombres' => 'Carlos', 'apellidos' => 'Ayuda', 'ci' => '5345678',
                'correo' => 'voluntario123@gmail.com', 'telefono' => '775678901',
                'direccion' => 'Zona Sur, calle Libertad 420, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Voluntario', 'is_recolector' => true,
                'licencia_conducir' => 'B-4521789', 'fecha_registro' => '2026-01-08',
            ],
            [
                'nombres' => 'María', 'apellidos' => 'Condori', 'ci' => '6456789',
                'correo' => 'maria.condori.recolector@emergencias.scz.bo', 'telefono' => '776789012',
                'direccion' => 'Warnes, barrio San José, Santa Cruz',
                'genero' => 'Femenino', 'rol' => 'Voluntario', 'is_recolector' => true,
                'licencia_conducir' => 'B-7890123', 'fecha_registro' => '2026-02-18',
            ],
            [
                'nombres' => 'Jorge', 'apellidos' => 'Salazar', 'ci' => '7567890',
                'correo' => 'jorge.salazar.recolector@emergencias.scz.bo', 'telefono' => '777890123',
                'direccion' => 'Montero, mercado municipal, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Voluntario', 'is_recolector' => true,
                'licencia_conducir' => 'C-3344556', 'fecha_registro' => '2026-03-22',
            ],
            [
                'nombres' => 'Patricia', 'apellidos' => 'Gonzales', 'ci' => '8678901',
                'correo' => 'patricia.gonzales.inventario@emergencias.scz.bo', 'telefono' => '778901234',
                'direccion' => 'El Torno, zona comercial, Santa Cruz',
                'genero' => 'Femenino', 'rol' => 'Almacenero', 'fecha_registro' => '2026-04-05',
            ],
            [
                'nombres' => 'Felipe', 'apellidos' => 'Roca', 'ci' => '9789012',
                'correo' => 'felipe.roca.inventario@emergencias.scz.bo', 'telefono' => '779012345',
                'direccion' => 'La Guardia, av. principal s/n, Santa Cruz',
                'genero' => 'Masculino', 'rol' => 'Voluntario', 'is_recolector' => false,
                'fecha_registro' => '2026-04-12',
            ],
            [
                'nombres' => 'Silvia', 'apellidos' => 'Balderrama', 'ci' => '4890123',
                'correo' => 'silvia.balderrama.inactiva@emergencias.scz.bo', 'telefono' => '760123456',
                'direccion' => 'Centro, calle Avaroa 88, Santa Cruz',
                'genero' => 'Femenino', 'rol' => 'Almacenero', 'estado' => 'Inactivo',
                'fecha_registro' => '2025-08-30',
            ],
        ];
    }
}
