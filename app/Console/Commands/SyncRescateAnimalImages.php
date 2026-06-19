<?php

namespace App\Console\Commands;

use App\Support\RescateMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Care;
use Modules\Rescate\Models\MedicalEvaluation;
use Modules\Rescate\Models\Release;
use Modules\Rescate\Models\Report;

class SyncRescateAnimalImages extends Command
{
    protected $signature = 'rescate:sync-animal-images {--force : Reemplazar imágenes existentes}';

    protected $description = 'Asigna o actualiza fotos de fauna por especie en hallazgos, hojas de vida y registros relacionados';

    public function handle(): int
    {
        if (! Schema::connection('rescate')->hasTable('animal_files')) {
            $this->warn('No existe la tabla rescate.animal_files.');

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $updated = 0;

        AnimalFile::with('species')->orderBy('id')->chunk(50, function ($files) use ($force, &$updated) {
            foreach ($files as $file) {
                RescateMedia::refreshAnimalFileImage($file, $force);
                $updated++;
            }
        });

        foreach ([Report::class, Release::class, Care::class, MedicalEvaluation::class] as $modelClass) {
            if (! Schema::connection('rescate')->hasTable((new $modelClass)->getTable())) {
                continue;
            }

            $modelClass::query()->whereNotNull('imagen_url')->orderBy('id')->chunk(50, function ($rows) use ($force, &$updated) {
                foreach ($rows as $row) {
                    $label = $this->labelForRow($row);
                    if ($label && $row->imagen_url) {
                        \App\Support\DemoImageDownloader::storeSpeciesImage($row->imagen_url, $label, $force);
                        $updated++;
                    }
                }
            });
        }

        $this->info("Imágenes de rescate sincronizadas ({$updated} registros procesados).");

        return self::SUCCESS;
    }

    private function labelForRow(object $row): ?string
    {
        if (isset($row->animalFile) && $row->animalFile?->species?->nombre) {
            return $row->animalFile->species->nombre;
        }

        if (method_exists($row, 'animalFile') && $row->relationLoaded('animalFile')) {
            return $row->animalFile?->species?->nombre;
        }

        return 'fauna';
    }
}
