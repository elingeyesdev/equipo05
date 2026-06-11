<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DemoImageDownloader
{
    public static function storeSpeciesImage(string $relativePath, string $speciesOrLabel, bool $force = false): ?string
    {
        if (! $force && Storage::disk('public')->exists($relativePath)) {
            $size = Storage::disk('public')->size($relativePath);
            if ($size > 8000) {
                return $relativePath;
            }
        }

        // Bypass external HTTP requests to avoid slow/hanging WSL/Docker network timeouts
        return self::storeGeneratedPlaceholder($relativePath, AnimalImageCatalog::seedFor($speciesOrLabel));
    }

    public static function downloadUrl(string $relativePath, string $url): ?string
    {
        try {
            $response = Http::timeout(1)
                ->withOptions(['allow_redirects' => true])
                ->withHeaders(['User-Agent' => 'Equipo05-DemoSeeder/1.0'])
                ->get($url);

            if ($response->successful() && strlen($response->body()) > 2000) {
                $dir = dirname($relativePath);
                if ($dir !== '.' && ! Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->makeDirectory($dir);
                }
                Storage::disk('public')->put($relativePath, $response->body());

                return $relativePath;
            }
        } catch (\Throwable) {
            // siguiente fallback
        }

        return null;
    }

    public static function storePlaceholder(string $relativePath, string $seed): ?string
    {
        return self::storeSpeciesImage($relativePath, $seed, true);
    }

    private static function storeGeneratedPlaceholder(string $relativePath, string $seed): ?string
    {
        $safeSeed = preg_replace('/[^a-zA-Z0-9_-]/', '', $seed) ?: 'fauna';

        if (function_exists('imagecreatetruecolor')) {
            $img = imagecreatetruecolor(640, 480);
            $bg = imagecolorallocate($img, 40 + (crc32($safeSeed) % 120), 80 + (crc32($safeSeed) % 80), 120);
            imagefill($img, 0, 0, $bg);
            $dir = dirname($relativePath);
            if ($dir !== '.' && ! Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            $full = Storage::disk('public')->path($relativePath);
            if (@imagejpeg($img, $full, 85)) {
                imagedestroy($img);

                return $relativePath;
            }
            imagedestroy($img);
        }

        return null;
    }
}
