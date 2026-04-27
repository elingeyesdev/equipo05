<?php

namespace App\Http\Controllers\Fusion;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AccesoPublicoController extends Controller
{
    public function logisticaSolicitud(): View
    {
        return view('fusion.modulos.publico-logistica-solicitud');
    }

    public function logisticaSolicitudStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'apellido' => ['nullable', 'string', 'max:120'],
            'ci' => ['required', 'string', 'max:40'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'comunidad' => ['required', 'string', 'max:120'],
            'provincia' => ['required', 'string', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'tipo_emergencia' => ['required', 'string', 'max:120'],
            'cantidad_personas' => ['required', 'integer', 'min:1'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_necesidad' => ['nullable', 'date'],
            'insumos_necesarios' => ['nullable', 'string'],
        ]);

        $conn = DB::connection('logistica');

        $solicitanteId = $conn->table('solicitante')->insertGetId([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'] ?? null,
            'ci' => $data['ci'],
            'telefono' => $data['telefono'] ?? null,
            'email' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $destinoId = $conn->table('destino')->insertGetId([
            'comunidad' => $data['comunidad'],
            'provincia' => $data['provincia'],
            'direccion' => $data['direccion'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $codigo = 'SOL-' . now()->format('YmdHis');

        $conn->table('solicitud')->insert([
            'estado' => 'pendiente',
            'codigo_seguimiento' => $codigo,
            'cantidad_personas' => $data['cantidad_personas'],
            'fecha_inicio' => $data['fecha_inicio'],
            'tipo_emergencia' => $data['tipo_emergencia'],
            'insumos_necesarios' => $data['insumos_necesarios'] ?? null,
            'id_solicitante' => $solicitanteId,
            'id_destino' => $destinoId,
            'fecha_solicitud' => now()->toDateString(),
            'aprobada' => 0,
            'apoyoaceptado' => 0,
            'fecha_necesidad' => $data['fecha_necesidad'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('publico.logistica.solicitud')
            ->with('success', 'Solicitud enviada correctamente.');
    }

    public function logisticaGaleria(): View
    {
        $conn = DB::connection('logistica');
        $schema = $conn->getSchemaBuilder();
        $paquetes = collect();

        if ($schema->hasTable('paquete')) {
            $query = $conn->table('paquete')
                ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
                ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
                ->select([
                    'paquete.id_paquete',
                    'paquete.codigo',
                    'paquete.fecha_entrega',
                    'destino.comunidad',
                ]);

            if ($schema->hasColumn('paquete', 'imagen')) {
                $query->addSelect('paquete.imagen');
            }

            if ($schema->hasColumn('paquete', 'updated_at')) {
                $query->orderByDesc('paquete.updated_at');
            } elseif ($schema->hasColumn('paquete', 'id_paquete')) {
                $query->orderByDesc('paquete.id_paquete');
            }

            $paquetes = $query->limit(24)->get();
        }

        return view('fusion.modulos.publico-logistica-galeria', compact('paquetes'));
    }

    public function cuadrillasMapa(): View
    {
        return view('fusion.modulos.publico-cuadrillas-mapa');
    }

    public function cuadrillasReporte(): View
    {
        return $this->renderPublicTable(
            'Cuadrillas - Reporte Público',
            'Canal de reporte ciudadano de incendios y emergencias',
            'cuadrillas',
            'reporte',
            'id_reporte'
        );
    }

    public function seguimientoInfo(): View
    {
        return $this->renderPublicTable(
            'Seguimiento de Voluntarios',
            'Vista pública informativa de actividad voluntaria',
            'seguimiento',
            'usuario',
            'id_usuario'
        );
    }

    private function renderPublicTable(
        string $titulo,
        string $subtitulo,
        string $connection,
        string $tabla,
        string $pk
    ): View {
        $columnas = [];
        $filas = collect();
        $total = 0;

        if (Schema::connection($connection)->hasTable($tabla)) {
            $columnas = Schema::connection($connection)->getColumnListing($tabla);
            $columnas = array_slice($columnas, 0, 8);

            $query = DB::connection($connection)->table($tabla);
            if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn($tabla, $pk)) {
                $query->orderByDesc($pk);
            }

            $filas = $query->limit(20)->get($columnas);
            $total = DB::connection($connection)->table($tabla)->count();
        }

        return view('fusion.modulos.acceso-publico', compact(
            'titulo',
            'subtitulo',
            'columnas',
            'filas',
            'total'
        ));
    }
}
