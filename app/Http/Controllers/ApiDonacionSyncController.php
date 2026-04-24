<?php

namespace App\Http\Controllers;

use App\Models\Campania;
use App\Models\Donacion;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiDonacionSyncController extends Controller
{
    public function syncDinero()
    {
        // ✅ Usamos la configuración centralizada
        $baseUrl = config('services.externos.donaciones_url');

        // 1) Consumir API externa
        $response = Http::get("{$baseUrl}/api/donaciones/dinero");

        if ($response->failed()) {
            return back()->withErrors('No se pudo conectar a la API de donaciones de dinero.');
        }

        $donacionesExternas = $response->json();

        DB::transaction(function () use ($donacionesExternas) {
            foreach ($donacionesExternas as $d) {

                // 1. USUARIO (DONANTE)
                $usuario = null;
                if (!empty($d['donante']['email'])) {
                    $email   = $d['donante']['email'];
                    $nombre  = $d['donante']['nombre']   ?? 'Donante';
                    $telefono= $d['donante']['telefono'] ?? null;

                    // Crea si no existe
                    $usuario = Usuario::firstOrCreate(
                        ['email' => $email],
                        [
                            'contrasena' => bcrypt('password'),
                            'nombre'     => $nombre,
                            'apellido'   => '', // Apellido vacío si viene todo junto
                            'telefono'   => $telefono,
                            'activo'     => true,
                        ]
                    );
                }

                // 2. CAMPAÑA
                $campania = null;
                if (!empty($d['id_campana'])) {
                    $campania = Campania::where('idexterno', $d['id_campana'])->first();
                }

                // 3. ESTADO Y MAPEO
                $estadoId = 2; // Confirmada
                $monto       = $d['dinero']['monto']          ?? 0;
                $descripcion = $d['observaciones']            ?? null;
                $fecha       = $d['fecha']                    ?? now();

                Donacion::updateOrCreate(
                    ['idexterno' => $d['id_donacion']],
                    [
                        'usuarioid'     => $usuario?->usuarioid,
                        'campaniaid'    => $campania?->campaniaid,
                        'monto'         => $monto,
                        'tipodonacion'  => 'Monetaria',
                        'descripcion'   => $descripcion,
                        'fechadonacion' => $fecha,
                        'estadoid'      => $estadoId,
                        'esanonima'     => $usuario ? false : true,
                    ]
                );
            }
        });

        return back()->with('success', 'Donaciones de dinero sincronizadas correctamente.');
    }
}