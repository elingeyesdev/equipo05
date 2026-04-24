<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\ImagenesSolicitudRecogida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImagenController extends Controller
{
    /**
     * POST /api/imagenes-solicitud-recogida
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'id_solicitud' => 'required|exists:solicitudes_recoleccion,id_solicitud',
            'imagen' => 'required|file|image|max:10240', // max 10MB
        ]);

        try {
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('solicitudes', $filename, 'public');

            $imagen = ImagenesSolicitudRecogida::create([
                'id_solicitud' => $validated['id_solicitud'],
                'ruta_imagen' => $path,
            ]);

            return response()->json([
                'id' => $imagen->id_imagen,
                'url' => Storage::url($path),
                'message' => 'Imagen subida exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error subiendo imagen: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al subir imagen',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        return ImagenesSolicitudRecogida::with('solicitud')->get();
    }

    public function store(Request $request)
    {
        return $this->upload($request);
    }

    public function show(string $id)
    {
        return ImagenesSolicitudRecogida::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}







