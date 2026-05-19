<?php

use App\Support\RescateMedia;

if (! function_exists('rescate_media_url')) {
    function rescate_media_url(?string $path, string $seed = 'rescate'): string
    {
        return RescateMedia::url($path, $seed);
    }
}

if (! function_exists('rescate_media_seed')) {
    /**
     * @param  \Modules\Rescate\Models\AnimalFile|object|null  $animalFile
     */
    function rescate_media_seed($animalFile): string
    {
        if ($animalFile === null) {
            return 'fauna';
        }

        $species = $animalFile->species?->nombre;
        if ($species) {
            return $species;
        }

        $name = $animalFile->animal->nombre ?? $animalFile->animal?->nombre ?? null;

        return $name ?: 'fauna';
    }
}
