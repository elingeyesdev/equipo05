<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\Species;

class RescateMedia
{
    public static function url(?string $storagePath, string $seed = 'rescate'): string
    {
        // Siempre priorizar la imagen subida por el usuario (hallazgo, evaluación, etc.).
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            return self::localMediaUrl($storagePath);
        }

        $publicRelative = AnimalImageCatalog::publicRelativePath($seed);
        if (is_file(public_path($publicRelative))) {
            return self::publicPath($publicRelative);
        }

        $speciesStorage = 'animal-files/species-'.AnimalImageCatalog::seedFor($seed).'.jpg';
        if (Storage::disk('public')->exists($speciesStorage)) {
            return self::localMediaUrl($speciesStorage);
        }

        self::ensureSpeciesImage($seed, false);

        if (is_file(public_path($publicRelative))) {
            return self::publicPath($publicRelative);
        }

        return self::localMediaUrl(self::ensureDefaultImage());
    }

    public static function localMediaUrl(string $storagePath): string
    {
        if (self::publicStorageIsHealthy()) {
            return self::publicPath('storage/'.$storagePath);
        }

        if (Route::has('rescate.media')) {
            return route('rescate.media', ['path' => $storagePath], false);
        }

        return '/storage/'.$storagePath;
    }

    private static function publicPath(string $relativePath): string
    {
        return '/'.ltrim(str_replace('\\', '/', $relativePath), '/');
    }

    /**
     * Descarga y guarda una imagen acorde a la especie en storage público.
     */
    public static function assignSpeciesImage(int $speciesId, string $directory = 'animal-files'): ?string
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

        self::ensureSpeciesImage($species->nombre, $force);

        $path = $animalFile->imagen_url ?: 'animal-files/file-'.$animalFile->id.'.jpg';
        $stored = DemoImageDownloader::storeSpeciesImage($path, $species->nombre, $force);

        if ($stored && Storage::disk('public')->exists($stored) && $animalFile->imagen_url !== $stored) {
            $animalFile->update(['imagen_url' => $stored]);
        }
    }

    public static function ensureSpeciesImage(string $speciesName, bool $force = false): ?string
    {
        $seed = AnimalImageCatalog::seedFor($speciesName);
        $storagePath = 'animal-files/species-'.$seed.'.jpg';
        $publicRelative = AnimalImageCatalog::publicRelativePath($speciesName);
        $publicAbsolute = public_path($publicRelative);

        if (! $force && Storage::disk('public')->exists($storagePath)) {
            $size = Storage::disk('public')->size($storagePath);
            if ($size > 15000 && is_file($publicAbsolute)) {
                return $storagePath;
            }
        }

        $stored = DemoImageDownloader::storeSpeciesImage($storagePath, $speciesName, true);
        if (! $stored || ! Storage::disk('public')->exists($stored)) {
            return null;
        }

        $dir = dirname($publicAbsolute);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        copy(Storage::disk('public')->path($stored), $publicAbsolute);

        return $stored;
    }

    /**
     * Garantiza imágenes en public/images/rescate/ para todo el catálogo (offline, sin depender de BD).
     */
    public static function ensureCatalogImages(bool $force = false): int
    {
        $updated = 0;
        foreach (AnimalImageCatalog::imageSeeds() as $seed) {
            if (self::ensureSpeciesImage($seed, $force)) {
                $updated++;
            }
        }

        self::ensureDefaultImage();

        return $updated;
    }

    public static function ensureDefaultImage(): string
    {
        $path = 'animal-files/default-fauna.jpg';
        if (! Storage::disk('public')->exists($path)) {
            DemoImageDownloader::storeSpeciesImage($path, 'fauna', true);
        }

        return $path;
    }

    private static function looksLikeRandomPlaceholder(string $storagePath): bool
    {
        $basename = strtolower(basename($storagePath));

        return str_contains($basename, 'picsum')
            || preg_match('/^(demo-|from_report_|copy_)/', $basename) === 1;
    }

    private static function publicStorageIsHealthy(): bool
    {
        $link = public_path('storage');
        if (! file_exists($link)) {
            return false;
        }

        $target = storage_path('app/public');
        if (is_link($link)) {
            $resolved = realpath($link);

            return $resolved !== false && str_starts_with($resolved, realpath($target));
        }

        if (PHP_OS_FAMILY === 'Windows' && is_file($link)) {
            return false;
        }

        return is_dir($link) && is_dir($link.DIRECTORY_SEPARATOR.'animal-files');
    }
}
