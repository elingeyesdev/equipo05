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
            if ($size > 20000) {
                return $relativePath;
            }
        }

        $url = AnimalImageCatalog::downloadUrlFor($speciesOrLabel);
        $downloaded = self::downloadUrl($relativePath, $url);
        if ($downloaded !== null) {
            return $downloaded;
        }

        return self::storeGeneratedPlaceholder($relativePath, AnimalImageCatalog::seedFor($speciesOrLabel));
    }

    public static function downloadUrl(string $relativePath, string $url): ?string
    {
        try {
            $response = Http::timeout(20)
                ->withOptions(['allow_redirects' => true, 'verify' => true])
                ->withHeaders([
                    'User-Agent' => 'Equipo05-DemoSeeder/1.0 (Laravel; +https://github.com/elingeyesdev/equipo05)',
                    'Accept' => 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                ])
                ->get($url);

            if ($response->successful() && strlen($response->body()) > 8000) {
                return self::writePublicFile($relativePath, $response->body());
            }
        } catch (\Throwable) {
            // intentar fallback
        }

        $body = @file_get_contents($url, false, stream_context_create([
            'http' => [
                'timeout' => 20,
                'header' => "User-Agent: Equipo05-DemoSeeder/1.0\r\nAccept: image/*\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]));

        if (is_string($body) && strlen($body) > 8000) {
            return self::writePublicFile($relativePath, $body);
        }

        return null;
    }

    private static function writePublicFile(string $relativePath, string $contents): ?string
    {
        $dir = dirname($relativePath);
        if ($dir !== '.' && ! Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }
        Storage::disk('public')->put($relativePath, $contents);

        return Storage::disk('public')->exists($relativePath) ? $relativePath : null;
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
