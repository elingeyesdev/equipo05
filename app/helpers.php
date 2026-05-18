<?php

use App\Support\RescateMedia;

if (! function_exists('rescate_media_url')) {
    function rescate_media_url(?string $path, string $seed = 'rescate'): string
    {
        return RescateMedia::url($path, $seed);
    }
}
