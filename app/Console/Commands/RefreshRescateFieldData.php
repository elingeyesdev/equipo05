<?php

namespace App\Console\Commands;

use App\Support\DemoImageDownloader;
use App\Support\RescateFieldLocations;
use Database\Seeders\RichRescateDemoSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Incendios\Models\FocosIncendio;
use Modules\Rescate\Models\IncidentType;
use Modules\Rescate\Models\Release;
use Modules\Rescate\Models\Report;

class RefreshRescateFieldData extends Command
{
    protected $signature = 'rescate:refresh-field-data {--images : Vuelve a descargar imágenes de hallazgos}';

    protected $description = 'Asigna ubicaciones reales, diversifica incidentes y vincula hallazgos de incendio con focos del módulo Incendios';

    public function handle(): int
    {
        if (! Schema::connection('rescate')->hasTable('reports')) {
            $this->error('Tabla rescate.reports no disponible.');

            return self::FAILURE;
        }

        $incidents = IncidentType::where('activo', true)->orderBy('id')->get()->keyBy('nombre');
        $conditions = DB::connection('rescate')->table('animal_conditions')
            ->where('activo', true)
            ->orderBy('id')
            ->get()
            ->keyBy('nombre');

        $conditionIds = array_values(array_filter([
            $conditions->get('Herido grave')?->id,
            $conditions->get('Quemaduras')?->id,
            $conditions->get('Desconocido')?->id,
        ]));

        $incidentRotation = array_values(array_filter([
            $incidents->get('Incendio')?->id,
            $incidents->get('Atropello')?->id,
            $incidents->get('Otro')?->id,
        ]));

        $fireIds = $this->fireFocusIds();
        $updatedReports = 0;
        $coordsRestored = 0;
        $linkedFires = 0;
        $demoIndex = 0;

        Report::with(['animals.animalFiles.species'])
            ->orderBy('id')
            ->chunkById(50, function ($reports) use (
                &$updatedReports,
                &$coordsRestored,
                &$linkedFires,
                &$demoIndex,
                $conditionIds,
                $incidentRotation,
                $fireIds,
                $incidents
            ) {
                foreach ($reports as $report) {
                    $payload = [];
                    $index = (int) $report->id;

                    if (RescateFieldLocations::looksLikeDemoAddress($report->direccion)) {
                        $location = RescateFieldLocations::get($demoIndex);
                        $demoIndex++;
                        $payload = [
                            'direccion' => $location['direccion'],
                            'latitud' => $location['lat'],
                            'longitud' => $location['lng'],
                            'tipo_incidente_id' => $incidentRotation[$demoIndex % max(1, count($incidentRotation))] ?? $report->tipo_incidente_id,
                            'condicion_inicial_id' => $conditionIds[$demoIndex % max(1, count($conditionIds))] ?? $report->condicion_inicial_id,
                        ];
                    } else {
                        $matched = RescateFieldLocations::matchByDireccion($report->direccion);
                        if ($matched !== null) {
                            $payload['latitud'] = $matched['lat'];
                            $payload['longitud'] = $matched['lng'];
                            $coordsRestored++;
                        }
                    }

                    $incidentId = $payload['tipo_incidente_id'] ?? $report->tipo_incidente_id;
                    $incidentName = $incidents->firstWhere('id', $incidentId)?->nombre
                        ?? $report->incidentType?->nombre;

                    if ($incidentName === 'Incendio' && $fireIds !== []) {
                        $payload['incendio_id'] = $fireIds[$index % count($fireIds)];
                        if (! $report->incendio_id) {
                            $linkedFires++;
                        }
                    } elseif ($incidentName !== 'Incendio') {
                        $payload['incendio_id'] = null;
                    }

                    if ($payload === []) {
                        continue;
                    }

                    $report->update($payload);
                    $updatedReports++;

                    if ($this->option('images') && ! empty($payload['direccion'])) {
                        $this->refreshReportImage($report);
                    }
                }
            });

        $updatedReleases = 0;
        Release::orderBy('id')->chunkById(50, function ($releases) use (&$updatedReleases) {
            foreach ($releases as $index => $release) {
                if (! RescateFieldLocations::looksLikeDemoAddress($release->direccion)) {
                    continue;
                }

                $area = RescateFieldLocations::releaseArea($index);
                $release->update([
                    'direccion' => $area['direccion'],
                    'latitud' => $area['lat'],
                    'longitud' => $area['lng'],
                ]);
                $updatedReleases++;
            }
        });

        $syncedTransfers = DB::connection('rescate')->update('
            UPDATE transfers t
            SET latitud = r.latitud, longitud = r.longitud, updated_at = NOW()
            FROM reports r
            WHERE t.reporte_id = r.id
              AND t.primer_traslado = true
              AND (t.latitud IS DISTINCT FROM r.latitud OR t.longitud IS DISTINCT FROM r.longitud)
        ');

        if ($this->option('images')) {
            $seeder = new RichRescateDemoSeeder;
            $seeder->setCommand($this);
            $seeder->runSpeciesImageRefresh();
        }

        $this->info("Hallazgos actualizados: {$updatedReports}");
        $this->info("Coordenadas restauradas por dirección: {$coordsRestored}");
        $this->info("Hallazgos vinculados a focos de incendio: {$linkedFires}");
        $this->info("Liberaciones con ubicación real: {$updatedReleases}");
        $this->info("Traslados sincronizados con hallazgo: {$syncedTransfers}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, int>
     */
    private function fireFocusIds(): array
    {
        try {
            if (! Schema::connection('incendios')->hasTable('focos_incendios')) {
                return [];
            }
        } catch (\Throwable) {
            return [];
        }

        return FocosIncendio::query()
            ->whereNotNull('coordenadas')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function refreshReportImage(Report $report): void
    {
        if (! $report->imagen_url) {
            return;
        }

        $report->loadMissing('animals.animalFiles.species');
        $species = $report->animals->first()?->animalFiles->first()?->species?->nombre;
        $label = $species ?: pathinfo((string) $report->imagen_url, PATHINFO_FILENAME);

        DemoImageDownloader::storeSpeciesImage((string) $report->imagen_url, (string) $label, true);
    }
}
