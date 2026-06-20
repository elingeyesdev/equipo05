<?php

namespace Database\Seeders;

use App\Support\DemoImageDownloader;
use App\Support\RescateAnimalNameCleaner;
use App\Support\RescateFieldLocations;
use App\Support\UnifiedPostgres;
use Modules\Incendios\Models\FocosIncendio;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalStatus;
use Modules\Rescate\Models\Care;
use Modules\Rescate\Models\CareFeeding;
use Modules\Rescate\Models\CareType;
use Modules\Rescate\Models\FeedingFrequency;
use Modules\Rescate\Models\FeedingPortion;
use Modules\Rescate\Models\FeedingType;
use Modules\Rescate\Models\Center;
use Modules\Rescate\Models\ContactMessage;
use Modules\Rescate\Models\IncidentType;
use Modules\Rescate\Models\MedicalEvaluation;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Release;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Rescuer;
use Modules\Rescate\Models\Species;
use Modules\Rescate\Models\TreatmentType;
use Modules\Rescate\Models\Veterinarian;

class RichRescateDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('rescate')->hasTable('animals')) {
            $this->command?->warn('Rescate: ejecuta migraciones del módulo antes del seed.');

            return;
        }

        $previous = config('database.default');
        $cacheDriver = config('cache.default');
        config([
            'database.default' => UnifiedPostgres::coreAuthConnection(),
            'cache.default' => 'array',
        ]);

        try {
            if (class_exists(\Modules\Rescate\Database\Seeders\RolesAndPermissionsSeeder::class)) {
                try {
                    (new \Modules\Rescate\Database\Seeders\RolesAndPermissionsSeeder)->run();
                } catch (\Throwable $e) {
                    $this->command?->warn('Rescate roles omitidos: '.$e->getMessage());
                }
            }

            try {
                $showcase = new \Modules\Rescate\Seeders\ShowcaseDataSeeder;
                if ($this->command) {
                    $showcase->setCommand($this->command);
                }
                $showcase->run();
            } catch (\Throwable $e) {
                $this->command?->warn('Rescate showcase omitido: '.$e->getMessage());
            }

            $this->seedBulkFauna();
            $this->downloadRescateImages();
            \App\Support\RescateMedia::ensureCatalogImages(false);
            $cleaned = RescateAnimalNameCleaner::cleanAll();
            if ($cleaned > 0) {
                $this->command?->info("Rescate: {$cleaned} nombres de animales sin sufijo demo.");
            }
        } finally {
            config(['database.default' => $previous, 'cache.default' => $cacheDriver]);
        }

        $this->command?->info('Rescate: fauna, reportes e imágenes demo ampliados.');
    }

    private function seedBulkFauna(): void
    {
        $center = Center::first();
        $status = AnimalStatus::first();
        $person = Person::first();
        $incidents = IncidentType::where('activo', true)->orderBy('id')->get()->keyBy('nombre');
        $conditions = DB::connection('rescate')->table('animal_conditions')
            ->where('activo', true)
            ->orderBy('id')
            ->get()
            ->keyBy('nombre');
        $fireIds = FocosIncendio::query()
            ->whereNotNull('coordenadas')
            ->orderByDesc('id')
            ->pluck('id')
            ->all();

        $incidentRotation = array_values(array_filter([
            $incidents->get('Incendio')?->id,
            $incidents->get('Atropello')?->id,
            $incidents->get('Otro')?->id,
        ]));
        $conditionIds = array_values(array_filter([
            $conditions->get('Herido grave')?->id,
            $conditions->get('Quemaduras')?->id,
            $conditions->get('Desconocido')?->id,
        ]));

        if (! $center || ! $status || ! $person || $incidentRotation === []) {
            return;
        }

        $fauna = [
            ['Zorro', 'Macho', 'zorro'],
            ['Perezoso', 'Hembra', 'perezoso'],
            ['Loro', 'Desconocido', 'loro'],
            ['Jaguar', 'Macho', 'jaguar'],
            ['Tapir', 'Hembra', 'tapir'],
            ['Guacamayo', 'Macho', 'guacamayo'],
            ['Serpiente', 'Desconocido', 'serpiente'],
            ['Mono capuchino', 'Macho', 'mono'],
            ['Tucán', 'Hembra', 'tucan'],
            ['Oso hormiguero', 'Macho', 'oso'],
            ['Ciervo de los pantanos', 'Hembra', 'ciervo'],
            ['Hurón', 'Macho', 'huron'],
            ['Perro callejero', 'Macho', 'perro'],
            ['Gato abandonado', 'Hembra', 'gato'],
            ['Boa', 'Desconocido', 'boa'],
            ['Águila', 'Macho', 'aguila'],
            ['Coati', 'Hembra', 'coati'],
            ['Nutria', 'Macho', 'nutria'],
            ['Tortuga', 'Hembra', 'tortuga'],
            ['Puercoespín', 'Macho', 'puercoespin'],
        ];

        $now = Carbon::now();

        foreach ($fauna as $i => [$especieNombre, $sexo, $seed]) {
            $species = Species::firstOrCreate(['nombre' => $especieNombre]);

            $location = RescateFieldLocations::get($i);
            $incidentId = $incidentRotation[$i % count($incidentRotation)];
            $incidentName = $incidents->firstWhere('id', $incidentId)?->nombre;
            $incendioId = ($incidentName === 'Incendio' && $fireIds !== [])
                ? $fireIds[$i % count($fireIds)]
                : null;

            $report = Report::firstOrCreate(
                ['direccion' => $location['direccion'], 'tipo_incidente_id' => $incidentId],
                [
                    'persona_id' => $person->id,
                    'aprobado' => $i % 5 === 0 ? 0 : 1,
                    'imagen_url' => 'reports/rich-'.$seed.'.jpg',
                    'observaciones' => 'Hallazgo en '.$location['direccion'].': '.$especieNombre.' requiere evaluación.',
                    'latitud' => $location['lat'],
                    'longitud' => $location['lng'],
                    'incendio_id' => $incendioId,
                    'condicion_inicial_id' => $conditionIds[$i % count($conditionIds)] ?? null,
                    'tamano' => ['pequeno', 'mediano', 'grande'][rand(0, 2)],
                    'puede_moverse' => (bool) rand(0, 1),
                    'urgencia' => rand(2, 5),
                ]
            );

            $animal = Animal::firstOrCreate(
                ['nombre' => $especieNombre, 'reporte_id' => $report->id],
                ['sexo' => $sexo, 'descripcion' => 'Ejemplar rescatado para seguimiento clínico.']
            );

            AnimalFile::firstOrCreate(
                ['animal_id' => $animal->id],
                [
                    'especie_id' => $species->id,
                    'imagen_url' => 'animal-files/rich-'.$seed.'.jpg',
                    'estado_id' => $status->id,
                    'centro_id' => $center->id,
                ]
            );
        }
    }

    public function runSpeciesImageRefresh(): void
    {
        $this->downloadRescateImages();

        AnimalFile::with('species')->orderBy('id')->each(function (AnimalFile $file) {
            \App\Support\RescateMedia::refreshAnimalFileImage($file, true);
        });
    }

    /**
     * Traslados, evaluaciones, cuidados y liberaciones para KPIs del dashboard.
     */
    public function enrichDashboardDemoData(): void
    {
        $rescuer = Rescuer::where('aprobado', true)->first();
        $veterinarian = Veterinarian::where('aprobado', true)->first();
        $careType = CareType::first();
        $careTypeAlim = CareType::whereRaw('LOWER(nombre) LIKE ?', ['%alim%'])->first() ?? $careType;
        $treatment = TreatmentType::first();
        $center = Center::first();
        $feedType = FeedingType::first();
        $feedFrequency = FeedingFrequency::first();
        $feedPortion = FeedingPortion::first();
        $statusEstable = AnimalStatus::whereRaw('LOWER(nombre) LIKE ?', ['%estable%'])->first() ?? AnimalStatus::first();

        if (! $rescuer || ! $veterinarian || ! $careType || ! $treatment || ! $center) {
            return;
        }

        $rescuerPerson = Person::find($rescuer->persona_id);
        $now = Carbon::now();

        Report::where('aprobado', false)->limit(6)->update(['aprobado' => true]);

        $files = AnimalFile::with(['animal.report', 'species', 'release'])->get();
        $released = 0;

        foreach ($files as $index => $file) {
            $animal = $file->animal;
            $report = $animal?->report;
            if (! $animal || ! $report) {
                continue;
            }

            $speciesLabel = $file->species?->nombre ?? 'fauna';
            $recentAt = $now->copy()->subDays($index % 14);

            DB::connection('rescate')->table('animal_files')->where('id', $file->id)->update([
                'created_at' => $recentAt,
                'updated_at' => $recentAt,
                'estado_id' => $statusEstable?->id ?? $file->estado_id,
            ]);

            DB::connection('rescate')->table('transfers')->updateOrInsert(
                ['reporte_id' => $report->id, 'primer_traslado' => true],
                [
                    'rescatista_id' => $rescuer->id,
                    'persona_id' => $rescuerPerson?->id,
                    'centro_id' => $file->centro_id ?? $center->id,
                    'observaciones' => sprintf(
                        'Primer traslado de %s desde %s hacia %s.',
                        $speciesLabel,
                        $report->direccion ?: 'punto de hallazgo',
                        Center::find($file->centro_id ?? $center->id)?->nombre ?? 'centro de rescate'
                    ),
                    'animal_id' => null,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'created_at' => $recentAt,
                    'updated_at' => $recentAt,
                ]
            );

            MedicalEvaluation::firstOrCreate(
                ['animal_file_id' => $file->id, 'fecha' => $recentAt->toDateString()],
                [
                    'tratamiento_id' => $treatment->id,
                    'tratamiento_texto' => 'Seguimiento clínico demo',
                    'descripcion' => 'Evaluación de '.$speciesLabel,
                    'diagnostico' => 'En observación',
                    'peso' => round(2 + ($index % 8), 1),
                    'temperatura' => 38.0 + ($index % 3) * 0.2,
                    'recomendacion' => 'Continuar monitoreo',
                    'apto_traslado' => $index % 3 !== 0,
                    'veterinario_id' => $veterinarian->id,
                    'imagen_url' => 'medical-evaluations/eval-'.$file->id.'.jpg',
                ]
            );

            $care = Care::firstOrCreate(
                ['hoja_animal_id' => $file->id, 'tipo_cuidado_id' => $careType->id, 'fecha' => $recentAt->toDateString()],
                [
                    'descripcion' => 'Cuidado diario — '.$speciesLabel,
                    'imagen_url' => 'cares/care-'.$file->id.'.jpg',
                ]
            );

            if ($feedType && $feedFrequency && $feedPortion && $index % 2 === 0) {
                $feedingCare = Care::firstOrCreate(
                    [
                        'hoja_animal_id' => $file->id,
                        'tipo_cuidado_id' => $careTypeAlim?->id ?? $careType->id,
                        'fecha' => $recentAt->copy()->subDay()->toDateString(),
                    ],
                    [
                        'descripcion' => 'Alimentación — '.$speciesLabel,
                        'imagen_url' => 'cares/feeding-'.$file->id.'.jpg',
                    ]
                );
                CareFeeding::firstOrCreate(
                    ['care_id' => $feedingCare->id],
                    [
                        'feeding_type_id' => $feedType->id,
                        'feeding_frequency_id' => $feedFrequency->id,
                        'feeding_portion_id' => $feedPortion->id,
                    ]
                );
            }

            DB::connection('rescate')->table('animal_histories')->updateOrInsert(
                ['animal_file_id' => $file->id],
                [
                    'changed_at' => $recentAt,
                    'estado_anterior' => 'En custodia',
                    'estado_nuevo' => 'Cuidado registrado',
                    'observaciones' => 'Actualización demo del historial clínico',
                    'old_values' => json_encode([], JSON_UNESCAPED_UNICODE),
                    'new_values' => json_encode([
                        'care' => [
                            'descripcion' => 'Registro demo de cuidado — '.$speciesLabel,
                            'fecha' => $recentAt->toDateString(),
                        ],
                    ], JSON_UNESCAPED_UNICODE),
                ]
            );

            if ($index % 7 === 0 && ! $file->release && $released < 4) {
                $releaseArea = RescateFieldLocations::releaseArea($released);
                Release::firstOrCreate(
                    ['animal_file_id' => $file->id],
                    [
                        'direccion' => $releaseArea['direccion'],
                        'detalle' => 'Liberación supervisada de '.$speciesLabel,
                        'latitud' => $releaseArea['lat'],
                        'longitud' => $releaseArea['lng'],
                        'aprobada' => true,
                        'imagen_url' => 'releases/release-'.$file->id.'.jpg',
                    ]
                );
                $released++;
            }
        }

        $citizen = Person::whereHas('reports')->first();
        if ($citizen?->usuario_id) {
            ContactMessage::firstOrCreate(
                ['user_id' => $citizen->usuario_id, 'motivo' => 'dashboard_demo'],
                ['mensaje' => 'Consulta demo sobre seguimiento de fauna rescatada.', 'leido' => false]
            );
            ContactMessage::firstOrCreate(
                ['user_id' => $citizen->usuario_id, 'motivo' => 'dashboard_demo_2'],
                ['mensaje' => 'Solicitud de actualización del estado de un hallazgo.', 'leido' => false]
            );
        }

        $this->command?->info('Rescate: datos enriquecidos (traslados, evaluaciones, cuidados, alimentación, historial, liberaciones).');
    }

    private function downloadRescateImages(): void
    {
        $downloaded = 0;

        $animalRows = DB::connection('rescate')->table('animal_files as af')
            ->join('species as s', 'af.especie_id', '=', 's.id')
            ->whereNotNull('af.imagen_url')
            ->select('af.imagen_url', 's.nombre')
            ->get();

        foreach ($animalRows as $row) {
            if (DemoImageDownloader::storeSpeciesImage((string) $row->imagen_url, (string) $row->nombre, true)) {
                $downloaded++;
            }
        }

        $reportRows = DB::connection('rescate')->table('reports')
            ->whereNotNull('imagen_url')
            ->pluck('imagen_url');

        foreach ($reportRows as $path) {
            $label = preg_replace('/^rich-/', '', pathinfo((string) $path, PATHINFO_FILENAME)) ?: 'fauna';
            if (DemoImageDownloader::storeSpeciesImage((string) $path, $label, true)) {
                $downloaded++;
            }
        }

        $releaseRows = DB::connection('rescate')->table('releases as r')
            ->join('animal_files as af', 'r.animal_file_id', '=', 'af.id')
            ->join('species as s', 'af.especie_id', '=', 's.id')
            ->whereNotNull('r.imagen_url')
            ->select('r.imagen_url', 's.nombre')
            ->get();

        foreach ($releaseRows as $row) {
            if (DemoImageDownloader::storeSpeciesImage((string) $row->imagen_url, (string) $row->nombre, true)) {
                $downloaded++;
            }
        }

        $carePaths = DB::connection('rescate')->table('cares as c')
            ->join('animal_files as af', 'c.hoja_animal_id', '=', 'af.id')
            ->join('species as s', 'af.especie_id', '=', 's.id')
            ->whereNotNull('c.imagen_url')
            ->select('c.imagen_url', 's.nombre')
            ->get();

        foreach ($carePaths as $row) {
            if (DemoImageDownloader::storeSpeciesImage((string) $row->imagen_url, (string) $row->nombre, true)) {
                $downloaded++;
            }
        }

        $evalPaths = DB::connection('rescate')->table('medical_evaluations as me')
            ->join('animal_files as af', 'me.animal_file_id', '=', 'af.id')
            ->join('species as s', 'af.especie_id', '=', 's.id')
            ->whereNotNull('me.imagen_url')
            ->select('me.imagen_url', 's.nombre')
            ->get();

        foreach ($evalPaths as $row) {
            if (DemoImageDownloader::storeSpeciesImage((string) $row->imagen_url, (string) $row->nombre, true)) {
                $downloaded++;
            }
        }

        $this->command?->info("Rescate: {$downloaded} imágenes de fauna en storage/app/public.");
    }
}
