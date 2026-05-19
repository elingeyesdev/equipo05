<?php

namespace Database\Seeders;

use App\Support\DemoImageDownloader;
use App\Support\UnifiedPostgres;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalStatus;
use Modules\Rescate\Models\Care;
use Modules\Rescate\Models\CareType;
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
        } finally {
            config(['database.default' => $previous, 'cache.default' => $cacheDriver]);
        }

        $this->command?->info('Rescate: fauna, reportes e imágenes demo ampliados.');
    }

    private function seedBulkFauna(): void
    {
        $center = Center::first();
        $status = AnimalStatus::first();
        $incident = IncidentType::first();
        $person = Person::first();

        if (! $center || ! $status || ! $incident || ! $person) {
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

            $direccion = 'Av. Demo rescate #'.($i + 10).', Santa Cruz';
            $report = Report::firstOrCreate(
                ['direccion' => $direccion, 'tipo_incidente_id' => $incident->id],
                [
                    'persona_id' => $person->id,
                    'aprobado' => $i % 4 === 0 ? 0 : 1,
                    'imagen_url' => 'reports/rich-'.$seed.'.jpg',
                    'observaciones' => 'Reporte demo masivo: '.$especieNombre.' necesita atención.',
                    'latitud' => -17.75 + ($i * 0.008),
                    'longitud' => -63.18 + ($i * 0.006),
                    'condicion_inicial_id' => DB::connection('rescate')->table('animal_conditions')->value('id'),
                    'tamano' => ['pequeno', 'mediano', 'grande'][rand(0, 2)],
                    'puede_moverse' => (bool) rand(0, 1),
                    'urgencia' => rand(2, 5),
                ]
            );

            $animalName = $especieNombre.' demo '.($i + 1);
            $animal = Animal::firstOrCreate(
                ['nombre' => $animalName, 'reporte_id' => $report->id],
                ['sexo' => $sexo, 'descripcion' => 'Ejemplar demo para presentación docente.']
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
    }

    /**
     * Traslados, evaluaciones, cuidados y liberaciones para KPIs del dashboard.
     */
    public function enrichDashboardDemoData(): void
    {
        $rescuer = Rescuer::where('aprobado', true)->first();
        $veterinarian = Veterinarian::where('aprobado', true)->first();
        $careType = CareType::first();
        $treatment = TreatmentType::first();
        $center = Center::first();
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
                ['reporte_id' => $report->id, 'animal_id' => $animal->id],
                [
                    'rescatista_id' => $rescuer->id,
                    'persona_id' => $rescuerPerson?->id,
                    'centro_id' => $file->centro_id ?? $center->id,
                    'observaciones' => 'Traslado demo — '.$speciesLabel,
                    'primer_traslado' => true,
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

            Care::firstOrCreate(
                ['hoja_animal_id' => $file->id, 'tipo_cuidado_id' => $careType->id, 'fecha' => $recentAt->toDateString()],
                [
                    'descripcion' => 'Cuidado diario — '.$speciesLabel,
                    'imagen_url' => 'cares/care-'.$file->id.'.jpg',
                ]
            );

            if ($index % 4 === 0 && ! $file->release && $released < 6) {
                Release::firstOrCreate(
                    ['animal_file_id' => $file->id],
                    [
                        'direccion' => 'Área de reintroducción demo #'.($index + 1),
                        'detalle' => 'Liberación supervisada de '.$speciesLabel,
                        'latitud' => -17.75 + ($index * 0.01),
                        'longitud' => -63.18 + ($index * 0.01),
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

        $this->command?->info('Rescate: datos de dashboard enriquecidos (traslados, evaluaciones, liberaciones).');
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

        $otherPaths = DB::connection('rescate')->table('cares')->whereNotNull('imagen_url')->pluck('imagen_url')
            ->merge(DB::connection('rescate')->table('medical_evaluations')->whereNotNull('imagen_url')->pluck('imagen_url'))
            ->merge(DB::connection('rescate')->table('releases')->whereNotNull('imagen_url')->pluck('imagen_url'))
            ->unique()
            ->filter();

        foreach ($otherPaths as $path) {
            if (DemoImageDownloader::storePlaceholder((string) $path, pathinfo((string) $path, PATHINFO_FILENAME))) {
                $downloaded++;
            }
        }

        $this->command?->info("Rescate: {$downloaded} imágenes de fauna en storage/app/public.");
    }
}
