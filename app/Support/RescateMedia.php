<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Species;

class RescateMedia
{
    public static function url(?string $storagePath, string $seed = 'rescate'): string
    {
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            $size = Storage::disk('public')->size($storagePath);
            if ($size > 3000 && ! self::looksLikeRandomPlaceholder($storagePath)) {
                return asset('storage/'.$storagePath);
            }
        }

        return AnimalImageCatalog::urlFor($seed);
    }

    /**
     * Descarga y guarda una imagen acorde a la especie en storage público.
     */
    public static function assignSpeciesImage(int $speciesId, string $directory = 'animal_files'): ?string
    {
        $species = Species::find($speciesId);
        if (! $species) {
            return null;
        }

        $slug = Str::slug($species->nombre) ?: 'fauna';
        $path = trim($directory, '/').'/'.$slug.'-'.uniqid().'.jpg';

        DemoImageDownloader::storeSpeciesImage($path, $species->nombre, true);

        return Storage::disk('public')->exists($path) ? $path : null;
    }

    public static function refreshAnimalFileImage(AnimalFile $animalFile, bool $force = true): void
    {
        if (! $animalFile->especie_id) {
            return;
        }

        $species = $animalFile->species ?? Species::find($animalFile->especie_id);
        if (! $species) {
            return;
        }

        $path = $animalFile->imagen_url ?: 'animal-files/file-'.$animalFile->id.'.jpg';
        DemoImageDownloader::storeSpeciesImage($path, $species->nombre, $force);

        if (Storage::disk('public')->exists($path) && $animalFile->imagen_url !== $path) {
            $animalFile->update(['imagen_url' => $path]);
        }
    }

    private static function looksLikeRandomPlaceholder(string $storagePath): bool
    {
        $basename = strtolower(basename($storagePath));

        return str_contains($basename, 'picsum')
            || preg_match('/^(demo-|from_report_|copy_)/', $basename) === 1;
    }
}
