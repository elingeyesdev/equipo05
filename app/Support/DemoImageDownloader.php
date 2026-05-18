<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DemoImageDownloader
{
    public static function storePlaceholder(string $relativePath, string $seed): ?string
    {
        if (Storage::disk('public')->exists($relativePath)) {
            return $relativePath;
        }

        $safeSeed = preg_replace('/[^a-zA-Z0-9_-]/', '', $seed) ?: 'demo';

        try {
            $response = Http::timeout(20)
                ->withOptions(['allow_redirects' => true])
                ->get('https://picsum.photos/seed/'.$safeSeed.'/640/480');

            if ($response->successful() && strlen($response->body()) > 1000) {
                Storage::disk('public')->put($relativePath, $response->body());

                return $relativePath;
            }
        } catch (\Throwable) {
            // fallback: imagen mínima JPEG generada con GD
        }

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
