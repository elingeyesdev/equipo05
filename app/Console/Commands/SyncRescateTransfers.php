<?php

namespace App\Console\Commands;

use App\Support\RescateFieldLocations;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Rescate\Models\Animal;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Center;
use Modules\Rescate\Models\IncidentType;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Rescuer;
use Modules\Rescate\Models\Transfer;
use Modules\Rescate\Services\Animal\AnimalTransferTransactionalService;

class SyncRescateTransfers extends Command
{
    protected $signature = 'rescate:sync-transfers {--pending=5 : Hallazgos aprobados sin traslado para la pestaña de primer traslado}';

    protected $description = 'Sincroniza traslados con animales y hallazgos existentes, crea traslados internos y hallazgos pendientes';

    public function handle(AnimalTransferTransactionalService $transferService): int
    {
        if (! Schema::connection('rescate')->hasTable('transfers')) {
            $this->error('Tabla rescate.transfers no disponible.');

            return self::FAILURE;
        }

        $rescuerPerson = Rescuer::with('person')->where('aprobado', true)->first()?->person;
        $centers = Center::orderBy('id')->get();
        if (! $rescuerPerson || $centers->isEmpty()) {
            $this->error('Faltan rescatista aprobado o centros de rescate.');

            return self::FAILURE;
        }

        $enriched = $this->enrichExistingFirstTransfers($rescuerPerson);
        $internal = $this->createInternalTransfers($transferService, $rescuerPerson, $centers);
        $pending = $this->ensurePendingFirstTransferReports((int) $this->option('pending'));

        $this->info("Primeros traslados enriquecidos: {$enriched}");
        $this->info("Traslados entre centros creados: {$internal}");
        $this->info("Hallazgos pendientes de primer traslado: {$pending}");
        $this->info('Total traslados: '.Transfer::count());

        return self::SUCCESS;
    }

    private function enrichExistingFirstTransfers(Person $defaultPerson): int
    {
        $count = 0;

        Transfer::with(['report', 'center'])
            ->where('primer_traslado', true)
            ->orderBy('id')
            ->chunkById(50, function ($transfers) use ($defaultPerson, &$count) {
                foreach ($transfers as $transfer) {
                    $report = $transfer->report;
                    if (! $report) {
                        continue;
                    }

                    $animal = Animal::with('animalFiles.species')
                        ->where('reporte_id', $report->id)
                        ->first();

                    $species = $animal?->animalFiles?->first()?->species?->nombre ?? $animal?->nombre ?? 'fauna';
                    $centerName = $transfer->center?->nombre ?? 'centro de rescate';
                    $location = $report->direccion ?: 'punto de hallazgo';

                    $payload = [
                        'persona_id' => $transfer->persona_id ?: $defaultPerson->id,
                        'centro_id' => $transfer->centro_id ?: $animal?->animalFiles?->first()?->centro_id,
                        'latitud' => $report->latitud,
                        'longitud' => $report->longitud,
                        'observaciones' => sprintf(
                            'Primer traslado de %s desde %s hacia %s.',
                            $species,
                            $location,
                            $centerName
                        ),
                    ];

                    $transfer->update(array_filter($payload, fn ($v) => $v !== null));
                    $count++;
                }
            });

        return $count;
    }

    private function createInternalTransfers(
        AnimalTransferTransactionalService $transferService,
        Person $person,
        $centers
    ): int {
        $created = 0;
        $centerIds = $centers->pluck('id')->all();

        AnimalFile::with(['animal', 'species', 'center'])
            ->whereDoesntHave('release')
            ->orderBy('id')
            ->get()
            ->each(function (AnimalFile $file, int $index) use ($transferService, $person, $centerIds, &$created) {
                if ($index % 2 !== 0) {
                    return;
                }

                $currentCenterId = (int) $file->centro_id;
                $destinations = array_values(array_filter($centerIds, fn ($id) => $id !== $currentCenterId));
                if ($destinations === []) {
                    return;
                }

                $destCenterId = $destinations[$index % count($destinations)];

                $alreadyMoved = Transfer::where('animal_id', $file->animal_id)
                    ->where('primer_traslado', false)
                    ->where('centro_id', $destCenterId)
                    ->exists();

                if ($alreadyMoved) {
                    return;
                }

                $species = $file->species?->nombre ?? $file->animal?->nombre ?? 'fauna';
                $from = $file->center?->nombre ?? 'centro anterior';
                $to = Center::find($destCenterId)?->nombre ?? 'centro destino';

                try {
                    $transferService->create([
                        'persona_id' => $person->id,
                        'animal_id' => $file->animal_id,
                        'centro_id' => $destCenterId,
                        'primer_traslado' => false,
                        'observaciones' => sprintf(
                            'Traslado interno de %s: de %s a %s por seguimiento clínico.',
                            $species,
                            $from,
                            $to
                        ),
                    ]);
                    $created++;
                } catch (\Throwable $e) {
                    $this->warn("Traslado interno omitido (animal #{$file->animal_id}): {$e->getMessage()}");
                }
            });

        return $created;
    }

    private function ensurePendingFirstTransferReports(int $target): int
    {
        $existing = Report::query()
            ->where('aprobado', true)
            ->whereDoesntHave('transfers', fn ($q) => $q->where('primer_traslado', true))
            ->count();

        $toCreate = max(0, $target - $existing);
        if ($toCreate === 0) {
            return $existing;
        }

        $citizen = Person::whereHas('reports')->first() ?? Person::first();
        $incidents = IncidentType::where('activo', true)->orderBy('id')->pluck('id')->all();
        $conditions = DB::connection('rescate')->table('animal_conditions')
            ->where('activo', true)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if (! $citizen || $incidents === []) {
            return $existing;
        }

        $now = Carbon::now();
        $offset = Report::max('id') ?? 0;

        for ($i = 0; $i < $toCreate; $i++) {
            $location = RescateFieldLocations::get($offset + $i);
            Report::create([
                'persona_id' => $citizen->id,
                'tipo_incidente_id' => $incidents[($offset + $i) % count($incidents)],
                'aprobado' => 1,
                'imagen_url' => null,
                'observaciones' => 'Hallazgo reportado en '.$location['direccion'].'. Pendiente de primer traslado.',
                'direccion' => $location['direccion'],
                'latitud' => $location['lat'],
                'longitud' => $location['lng'],
                'condicion_inicial_id' => $conditions[($offset + $i) % max(1, count($conditions))] ?? null,
                'tamano' => ['pequeno', 'mediano', 'grande'][$i % 3],
                'puede_moverse' => $i % 2 === 0,
                'urgencia' => 3 + ($i % 3),
                'created_at' => $now->copy()->subHours($i + 1),
                'updated_at' => $now->copy()->subHours($i + 1),
            ]);
        }

        return $existing + $toCreate;
    }
}
