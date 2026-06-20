<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class RescateMediaController extends Controller
{
    public function show(Request $request, string $path): Response
    {
        $path = str_replace(['\\', '..'], ['/', ''], $path);
        $path = ltrim($path, '/');

        abort_unless($path !== '' && Storage::disk('public')->exists($path), 404);

        $absolute = Storage::disk('public')->path($path);
        $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'image/jpeg',
        };

        return response()->file($absolute, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
