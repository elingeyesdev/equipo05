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
use Modules\Rescate\Models\Center;
use Modules\Rescate\Models\IncidentType;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Species;

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

    private function downloadRescateImages(): void
    {
        $paths = DB::connection('rescate')->table('reports')
            ->whereNotNull('imagen_url')
            ->pluck('imagen_url')
            ->merge(
                DB::connection('rescate')->table('animal_files')->whereNotNull('imagen_url')->pluck('imagen_url')
            )
            ->merge(
                DB::connection('rescate')->table('cares')->whereNotNull('imagen_url')->pluck('imagen_url')
            )
            ->merge(
                DB::connection('rescate')->table('medical_evaluations')->whereNotNull('imagen_url')->pluck('imagen_url')
            )
            ->merge(
                DB::connection('rescate')->table('releases')->whereNotNull('imagen_url')->pluck('imagen_url')
            )
            ->unique()
            ->filter();

        $downloaded = 0;
        foreach ($paths as $path) {
            $seed = pathinfo((string) $path, PATHINFO_FILENAME);
            if (DemoImageDownloader::storePlaceholder((string) $path, $seed)) {
                $downloaded++;
            }
        }

        $this->command?->info("Rescate: {$downloaded} imágenes placeholder en storage/app/public.");
    }
}
