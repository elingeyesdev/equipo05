<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class RescateMedia
{
    public static function url(?string $storagePath, string $seed = 'rescate'): string
    {
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            return asset('storage/'.$storagePath);
        }

        $safeSeed = preg_replace('/[^a-zA-Z0-9_-]/', '', $seed) ?: 'rescate';

        return 'https://picsum.photos/seed/'.$safeSeed.'/400/300';
    }
}
