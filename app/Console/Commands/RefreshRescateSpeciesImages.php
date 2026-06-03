<?php

namespace App\Console\Commands;

use App\Support\RescateMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Release;

class RefreshRescateSpeciesImages extends Command
{
    protected $signature = 'rescate:refresh-species-images';

    protected $description = 'Reasigna fotos de animales según su especie (corrige imágenes aleatorias de demo)';

    public function handle(): int
    {
        $files = 0;
        AnimalFile::with('species')->orderBy('id')->chunkById(50, function ($chunk) use (&$files) {
            foreach ($chunk as $animalFile) {
                RescateMedia::refreshAnimalFileImage($animalFile, true);
                $files++;
            }
        });

        $releases = 0;
        Release::with(['animalFile.species'])->orderBy('id')->chunkById(50, function ($chunk) use (&$releases) {
            foreach ($chunk as $release) {
                $species = $release->animalFile?->species;
                if (! $species) {
                    continue;
                }
                $path = $release->imagen_url ?: 'releases/release-'.$release->id.'.jpg';
                \App\Support\DemoImageDownloader::storeSpeciesImage($path, $species->nombre, true);
                if ($release->imagen_url !== $path) {
                    $release->update(['imagen_url' => $path]);
                }
                $releases++;
            }
        });

        $reports = DB::connection('rescate')->table('reports')
            ->whereNotNull('imagen_url')
            ->count();

        $this->info("Hojas de vida actualizadas: {$files}");
        $this->info("Liberaciones actualizadas: {$releases}");
        $this->info("Reportes con imagen propia (sin cambiar): {$reports}");

        return self::SUCCESS;
    }
}
