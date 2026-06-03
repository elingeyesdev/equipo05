<?php

namespace Modules\Rescate\Services\Fire;

use App\Support\UnifiedPostgres;
use Illuminate\Support\Collection;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Release;
use Modules\Rescate\Models\Report;
use Modules\Rescate\Models\Species;

class MapaCampoDataService
{
    public function __construct(
        private readonly FocosCalorService $focosCalorService,
        private readonly ExternalFireReportsService $externalFireReportsService,
    ) {}

    /**
     * @return array{
     *     reports: Collection,
     *     focosCalorFormatted: array,
     *     releases: Collection,
     *     species: Collection,
     *     operationalFiresFormatted: array,
     *     firesMapSource: string
     * }
     */
    public function build(): array
    {
        $reports = Report::with(['person', 'condicionInicial', 'incidentType'])
            ->where('aprobado', 1)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderByDesc('id')
            ->get()
            ->map(function (Report $report) {
                $hasAnimalFiles = AnimalFile::whereHas('animal', function ($q) use ($report) {
                    $q->where('reporte_id', $report->id);
                })->exists();

                return [
                    'id' => $report->id,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'urgencia' => $report->urgencia,
                    'incendio_id' => $report->incendio_id,
                    'direccion' => $report->direccion,
                    'tiene_hoja_vida' => $hasAnimalFiles,
                    'condicion_inicial' => $report->condicionInicial ? [
                        'nombre' => $report->condicionInicial->nombre,
                    ] : null,
                    'incident_type' => $report->incidentType ? [
                        'nombre' => $report->incidentType->nombre,
                    ] : null,
                ];
            });

        if ($this->shouldIncludeDemoHallazgo()) {
            $reports->push([
                'id' => 'simulado',
                'latitud' => '-17.718397',
                'longitud' => '-60.774994',
                'urgencia' => 5,
                'incendio_id' => 1,
                'direccion' => 'San Jose de Chiquitos, Santa Cruz, Bolivia',
                'tiene_hoja_vida' => false,
                'condicion_inicial' => ['nombre' => 'Hallazgo'],
                'incident_type' => ['nombre' => 'Incendio forestal'],
            ]);
        }

        $focosCalor = $this->focosCalorService->getRecentHotspotsWithFallback(2);
        $focosCalorFormatted = $this->focosCalorService->formatForMap($focosCalor);

        $releases = Release::with(['animalFile.species', 'animalFile.animal', 'animalFile.animalStatus'])
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Release $release) {
                $animalFile = $release->animalFile;

                return [
                    'id' => $release->id,
                    'latitud' => $release->latitud,
                    'longitud' => $release->longitud,
                    'direccion' => $release->direccion,
                    'detalle' => $release->detalle,
                    'fecha' => $release->created_at ? $release->created_at->format('d/m/Y') : null,
                    'especie_id' => $animalFile?->especie_id,
                    'especie' => $animalFile?->species ? [
                        'id' => $animalFile->species->id,
                        'nombre' => $animalFile->species->nombre,
                    ] : null,
                    'animal' => $animalFile?->animal ? [
                        'id' => $animalFile->animal->id,
                        'nombre' => $animalFile->animal->nombre,
                    ] : null,
                    'imagen_url' => $release->imagen_url,
                ];
            });

        $speciesIds = $releases->pluck('especie_id')->filter()->unique();
        $species = Species::whereIn('id', $speciesIds)->orderBy('nombre')->get(['id', 'nombre']);

        $fires = $this->externalFireReportsService->getOperationalFiresForMap();

        return [
            'reports' => $reports,
            'focosCalorFormatted' => $focosCalorFormatted,
            'releases' => $releases,
            'species' => $species,
            'operationalFiresFormatted' => $fires['items'],
            'firesMapSource' => $fires['source'],
        ];
    }

    private function shouldIncludeDemoHallazgo(): bool
    {
        if (UnifiedPostgres::enabled()) {
            return false;
        }

        return filter_var(env('APP_DEMO_SIMULATED_FIRES', false), FILTER_VALIDATE_BOOL);
    }
}
