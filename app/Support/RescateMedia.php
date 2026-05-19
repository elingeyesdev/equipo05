<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class RescateMedia
{
    public static function url(?string $storagePath, string $seed = 'rescate'): string
    {
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            $size = Storage::disk('public')->size($storagePath);
            if ($size > 8000) {
                return asset('storage/'.$storagePath);
            }
        }

        return AnimalImageCatalog::urlFor($seed);
    }
}
