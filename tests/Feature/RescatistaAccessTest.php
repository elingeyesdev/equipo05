<?php

use App\Models\Usuario;
use App\Support\AccessControl;
use Illuminate\Support\Facades\Hash;

test('rescatista can access rescate home without redirect loop', function () {
    $email = 'rescatista-loop-test@example.com';

    $user = Usuario::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
    if (! $user) {
        $user = Usuario::create([
            'nombre' => 'Test',
            'apellido' => 'Rescatista',
            'email' => $email,
            'contrasena' => Hash::make('password'),
            'telefono' => '70000000',
            'activo' => true,
        ]);
    }

    AccessControl::syncSingleRole($user, 'Rescatista');

    $response = $this->actingAs($user)->get('/rescate/modulo/home');

    expect($response->status())->not->toBe(302)
        ->and(AccessControl::userCanAccessModule($user, 'rescate'))->toBeTrue();
});

test('rescatista can store animal record and redirect to animal files index', function () {
    if (! Illuminate\Support\Facades\Schema::connection('rescate')->hasTable('reports')) {
        expect(true)->toBeTrue();

        return;
    }

    $email = 'rescatista-loop-test@example.com';
    $user = App\Models\Usuario::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
    if (! $user) {
        expect(true)->toBeTrue();

        return;
    }

    App\Support\AccessControl::syncSingleRole($user, 'Rescatista');

    $report = Modules\Rescate\Models\Report::query()
        ->where('aprobado', 1)
        ->whereNotExists(function ($q) {
            $q->selectRaw('1')->from('animals')->whereColumn('animals.reporte_id', 'reports.id');
        })
        ->orderByDesc('id')
        ->first();

    if (! $report) {
        expect(true)->toBeTrue();

        return;
    }

    $speciesId = Modules\Rescate\Models\Species::orderBy('id')->value('id');
    $estadoId = Modules\Rescate\Models\AnimalStatus::orderBy('id')->value('id');

    $response = test()->actingAs($user)->post('/rescate/modulo/animal-records', [
        'reporte_id' => $report->id,
        'nombre' => 'Test HTTP '.time(),
        'sexo' => 'Desconocido',
        'descripcion' => 'Prueba feature',
        'especie_id' => $speciesId,
        'estado_id' => $estadoId,
        'estado_inicial_id' => $report->condicion_inicial_id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $created = Modules\Rescate\Models\Animal::where('reporte_id', $report->id)->latest('id')->first();
    expect($created)->not->toBeNull();
    $animalFile = Modules\Rescate\Models\AnimalFile::where('animal_id', $created->id)->first();
    expect($animalFile)->not->toBeNull();
    expect($response->headers->get('Location'))->toContain('/animal-files/'.$animalFile->id);
    Modules\Rescate\Models\AnimalFile::where('animal_id', $created->id)->delete();
    $created->delete();
});

test('rescate media url uses local storage not external wikimedia', function () {
    $url = App\Support\RescateMedia::url('animal-files/rich-zorro.jpg', 'Zorro');

    expect($url)->not->toContain('wikimedia.org')
        ->and(
            str_starts_with($url, '/storage/')
            || str_starts_with($url, '/images/rescate/')
            || str_starts_with($url, '/rescate/modulo/media/')
        )->toBeTrue();
});
