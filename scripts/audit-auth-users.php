<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$core = DB::connection('core')->table('usuarios')
    ->select('usuarioid', 'email', 'activo', 'contrasena')
    ->get();

$inv = DB::connection('inventario')->table('usuarios')
    ->select('id_usuario', 'correo', 'estado', 'contrasena', 'nombres', 'apellidos')
    ->get();

$hashOk = static fn (?string $h): bool => is_string($h) && (
    str_starts_with($h, '$2y$') || str_starts_with($h, '$2a$') || str_starts_with($h, '$2b$')
);

$coreByEmail = [];
foreach ($core as $u) {
    $coreByEmail[strtolower(trim($u->email))] = $u;
}

echo "=== AUDITORÍA DE AUTENTICACIÓN ===\n";
echo 'Core usuarios: '.$core->count()."\n";
echo 'Inventario usuarios: '.$inv->count()."\n";

$invNotInCore = [];
foreach ($inv as $u) {
    $email = strtolower(trim((string) $u->correo));
    if ($email === '' || isset($coreByEmail[$email])) {
        continue;
    }
    $invNotInCore[] = $u;
}

echo 'Inventario SIN cuenta en core (no pueden /login): '.count($invNotInCore)."\n";
foreach (array_slice($invNotInCore, 0, 15) as $u) {
    echo "  - {$u->correo} | {$u->nombres} {$u->apellidos} | hash=".($hashOk($u->contrasena) ? 'ok' : 'BAD')."\n";
}

$badCore = $core->filter(fn ($u) => ! $hashOk($u->contrasena));
echo 'Core con hash inválido: '.$badCore->count()."\n";
foreach ($badCore->take(10) as $u) {
    echo "  - {$u->email}\n";
}

$inactiveCore = $core->where('activo', false);
echo 'Core inactivos: '.$inactiveCore->count()."\n";

echo "\n=== TODOS LOS USUARIOS CORE ===\n";
foreach ($core as $u) {
    $roles = DB::connection('core')->table('model_has_roles')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('model_has_roles.model_id', $u->usuarioid)
        ->where('model_has_roles.model_type', 'like', '%Usuario%')
        ->pluck('roles.name')
        ->implode(', ');
    echo "  [{$u->usuarioid}] {$u->email} | activo=".($u->activo ? '1' : '0')." | roles=[{$roles}] | hash=".($hashOk($u->contrasena) ? 'ok' : 'BAD')."\n";
}

echo "\n=== USUARIOS INVENTARIO ===\n";
foreach ($inv as $u) {
    $inCore = isset($coreByEmail[strtolower(trim((string) $u->correo))]) ? 'SI' : 'NO';
    echo "  [{$u->id_usuario}] {$u->correo} | en_core={$inCore} | estado={$u->estado}\n";
}
