<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Campania;
use App\Models\Donacion;

class SyncDonacionesDinero extends Command
{
    protected $signature = 'sync:donaciones-dinero';
    protected $description = 'Sincroniza donaciones de dinero desde API externa';

    public function handle(): int
    {
        // IMPORTANTE: Donaciones depende de Campañas
        $this->call('sync:campanias');

        $baseUrl = config('services.externos.donaciones_url');
        $resp = Http::timeout(25)->get("{$baseUrl}/api/donaciones/dinero");

        if ($resp->failed()) {
            $this->error('No se pudo conectar a /api/donaciones/dinero');
            return self::FAILURE;
        }

        $donacionesExternas = $resp->json();

        DB::transaction(function () use ($donacionesExternas) {
            foreach ($donacionesExternas as $d) {

                $usuario = null;
                if (!empty($d['donante']['email'])) {
                    $email    = $d['donante']['email'];
                    $nombre   = $d['donante']['nombre'] ?? 'Donante';
                    $telefono = $d['donante']['telefono'] ?? null;

                    $usuario = Usuario::firstOrCreate(
                        ['email' => $email],
                        [
                            'contrasena' => bcrypt('password'),
                            'nombre'     => $nombre,
                            'apellido'   => '',
                            'telefono'   => $telefono,
                            'activo'     => true,
                        ]
                    );
                }

                $campania = null;
                if (!empty($d['id_campana'])) {
                    $campania = Campania::where('idexterno', $d['id_campana'])->first();
                }

                Donacion::updateOrCreate(
                    ['idexterno' => $d['id_donacion']],
                    [
                        'usuarioid'     => $usuario?->usuarioid,
                        'campaniaid'    => $campania?->campaniaid,
                        'monto'         => $d['dinero']['monto'] ?? 0,
                        'tipodonacion'  => 'Monetaria',
                        'descripcion'   => $d['observaciones'] ?? null,
                        'fechadonacion' => $d['fecha'] ?? now(),
                        'estadoid'      => 2, // Confirmada
                        'esanonima'     => $usuario ? false : true,
                    ]
                );
            }
        });

        $this->info('Donaciones dinero sincronizadas OK.');
        return self::SUCCESS;
    }
}
