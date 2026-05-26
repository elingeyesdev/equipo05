<?php

namespace App\Services;

use App\Models\Campania;
use App\Models\Donacion;
use App\Models\Ext\ExtAlmacen;
use App\Models\Ext\ExtCategoriaProducto;
use App\Models\Ext\ExtEstante;
use App\Models\Ext\ExtEspacio;
use App\Models\Ext\ExtProducto;
use App\Models\TrazabilidadItem;
use App\Support\UnifiedPostgres;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Inventario\Models\Almacene;
use Modules\Inventario\Models\Campana;
use Modules\Inventario\Models\CategoriasProducto;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\DonacionesDinero;
use Modules\Inventario\Models\Espacio;
use Modules\Inventario\Models\Estante;
use Modules\Inventario\Models\Producto;
use Modules\Inventario\Models\UbicacionesDonacione;

/**
 * Sincroniza datos del módulo inventario (esquema inventario) hacia
 * transparencia (ext_* y trazabilidad_items) para que reportes y trazabilidad
 * reflejen lo creado en la UI de inventario sin depender de APIs externas.
 */
class UnifiedDataSyncService
{
    public function inventarioDisponible(): bool
    {
        try {
            return Schema::connection('inventario')->hasTable('almacenes');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, int>
     */
    public function syncAllFromInventario(): array
    {
        if (! $this->inventarioDisponible()) {
            return ['almacenes' => 0, 'categorias' => 0, 'campanias' => 0, 'trazabilidad' => 0, 'donaciones_dinero' => 0];
        }

        $stats = [];

        DB::connection(UnifiedPostgres::transparenciaConnection())->transaction(function () use (&$stats) {
            $stats['campanias'] = $this->syncCampaniasFromInventario();
            $stats['categorias'] = $this->syncCategoriasProductosFromInventario();
            $stats['almacenes'] = $this->syncAlmacenesFromInventario();
            $stats['trazabilidad'] = $this->syncTrazabilidadItemsFromInventario();
            $stats['donaciones_dinero'] = $this->syncDonacionesDineroFromInventario();
        });

        return $stats;
    }

    public function syncAlmacenesFromInventario(): int
    {
        if (! $this->inventarioDisponible()) {
            return 0;
        }

        $count = 0;

        Almacene::with(['estantes.espacios'])->orderBy('id_almacen')->each(function (Almacene $alm) use (&$count) {
            $almacenLocal = ExtAlmacen::updateOrCreate(
                ['idexterno' => $alm->id_almacen],
                [
                    'nombre'    => $alm->nombre,
                    'direccion' => $alm->direccion,
                    'latitud'   => $alm->latitud,
                    'longitud'  => $alm->longitud,
                ]
            );
            $count++;

            foreach ($alm->estantes as $est) {
                $estanteLocal = ExtEstante::updateOrCreate(
                    ['idexterno' => $est->id_estante],
                    [
                        'almacenid'      => $almacenLocal->almacenid,
                        'codigo_estante' => $est->codigo_estante,
                    ]
                );

                foreach ($est->espacios as $esp) {
                    ExtEspacio::updateOrCreate(
                        ['idexterno' => $esp->id_espacio],
                        [
                            'estanteid'      => $estanteLocal->estanteid,
                            'codigo_espacio' => $esp->codigo_espacio,
                            'estado'         => $esp->estado ?? 'disponible',
                        ]
                    );
                }
            }
        });

        return $count;
    }

    public function syncCategoriasProductosFromInventario(): int
    {
        if (! $this->inventarioDisponible()) {
            return 0;
        }

        $count = 0;

        CategoriasProducto::with('productos')->orderBy('id_categoria')->each(function (CategoriasProducto $cat) use (&$count) {
            $categoriaLocal = ExtCategoriaProducto::updateOrCreate(
                ['idexterno' => $cat->id_categoria],
                ['nombre' => $cat->nombre]
            );
            $count++;

            foreach ($cat->productos as $prod) {
                ExtProducto::updateOrCreate(
                    ['idexterno' => $prod->id_producto],
                    [
                        'categoriaid'   => $categoriaLocal->categoriaid,
                        'nombre'        => $prod->nombre,
                        'unidad_medida' => $prod->unidad_medida,
                    ]
                );
            }
        });

        return $count;
    }

    public function syncCampaniasFromInventario(): int
    {
        if (! $this->inventarioDisponible() || ! Schema::connection('inventario')->hasTable('campanas')) {
            return 0;
        }

        $count = 0;

        Campana::orderBy('id_campana')->each(function (Campana $c) use (&$count) {
            $campania = Campania::firstOrNew(['idexterno' => $c->id_campana]);

            $campania->titulo      = $c->nombre;
            $campania->descripcion = $c->descripcion ?? '';
            $campania->fechainicio = $c->fecha_inicio;
            $campania->fechafin    = $c->fecha_fin;
            $campania->imagenurl   = $c->imagen_banner;

            if (! $campania->exists) {
                $campania->metarecaudacion = 0;
                $campania->montorecaudado  = 0;
                $campania->usuarioidcreador = 1;
                $campania->activa = true;
            }

            $campania->save();
            $count++;
        });

        return $count;
    }

    /**
     * Replica campaña creada en transparencia hacia inventario.campanas.
     */
    public function mirrorCampaniaToInventario(Campania $campania): void
    {
        if (! $this->inventarioDisponible() || ! Schema::connection('inventario')->hasTable('campanas')) {
            return;
        }

        try {
            if ($campania->idexterno) {
                Campana::updateOrCreate(
                    ['id_campana' => $campania->idexterno],
                    [
                        'nombre'        => $campania->titulo,
                        'descripcion'   => $campania->descripcion,
                        'fecha_inicio'  => $campania->fechainicio,
                        'fecha_fin'     => $campania->fechafin,
                        'imagen_banner' => $campania->imagenurl,
                    ]
                );

                return;
            }

            $inv = Campana::create([
                'nombre'        => $campania->titulo,
                'descripcion'   => $campania->descripcion,
                'fecha_inicio'  => $campania->fechainicio,
                'fecha_fin'     => $campania->fechafin,
                'imagen_banner' => $campania->imagenurl,
            ]);

            $campania->idexterno = $inv->id_campana;
            $campania->save();
        } catch (\Throwable $e) {
            Log::warning('mirrorCampaniaToInventario: '.$e->getMessage());
        }
    }

    public function syncTrazabilidadItemsFromInventario(): int
    {
        if (! $this->inventarioDisponible()) {
            return 0;
        }

        $this->syncAlmacenesFromInventario();
        $this->syncCategoriasProductosFromInventario();
        $this->syncCampaniasFromInventario();

        $count = 0;

        if (! Schema::connection('inventario')->hasTable('ubicaciones_donaciones')) {
            return 0;
        }

        UbicacionesDonacione::with([
            'espacio.estante.almacene',
            'donacionDetalle.producto.categoriaProducto',
            'donacionDetalle.donacion.donante',
            'donacionDetalle.donacion.campana',
            'donacionDetalle.paqueteDetalles.paquete',
        ])->orderBy('id_ubicacion')->chunk(200, function ($ubicaciones) use (&$count) {
            foreach ($ubicaciones as $ubic) {
                $det = $ubic->donacionDetalle;
                if (! $det) {
                    continue;
                }

                $don = $det->donacion;
                if (! $don || ! in_array($don->tipo, ['especie', 'ropa'], true)) {
                    continue;
                }

                $espacio = $ubic->espacio;
                $estante = $espacio?->estante;
                $almacen = $estante?->almacene;

                $almacenLocal = $almacen
                    ? ExtAlmacen::where('idexterno', $almacen->id_almacen)->first()
                    : null;
                $estanteLocal = $estante
                    ? ExtEstante::where('idexterno', $estante->id_estante)->first()
                    : null;
                $espacioLocal = $espacio
                    ? ExtEspacio::where('idexterno', $espacio->id_espacio)->first()
                    : null;

                $productoExt = $det->producto
                    ? ExtProducto::where('idexterno', $det->producto->id_producto)->first()
                    : null;

                $campaniaLocal = $don->id_campana
                    ? Campania::where('idexterno', $don->id_campana)->first()
                    : null;

                $paqueteDet = $det->paqueteDetalles->first();
                $paquete = $paqueteDet?->paquete;

                $codigoUnico = 'DON'.$don->id_donacion.'-DET'.$det->id_detalle;

                $ubicacionTexto = trim(
                    ($almacen?->nombre ?? '').' / '.
                    ($estante?->codigo_estante ?? '').' / '.
                    ($espacio?->codigo_espacio ?? '')
                );

                TrazabilidadItem::updateOrCreate(
                    [
                        'id_donacion_externa' => $don->id_donacion,
                        'id_detalle_externo'  => $det->id_detalle,
                    ],
                    [
                        'campaniaid'              => $campaniaLocal?->campaniaid,
                        'id_campana_externa'      => $don->id_campana,
                        'campania_nombre'         => $don->campana?->nombre,
                        'codigo_unico'            => $codigoUnico,
                        'productoid'              => $productoExt?->productoid,
                        'nombre_producto'         => $det->producto?->nombre,
                        'categoria_producto'      => $det->producto?->categoriaProducto?->nombre,
                        'cantidad_donada'         => $det->cantidad,
                        'cantidad_ubicada'        => $ubic->cantidad_ubicada ?? $det->cantidad,
                        'unidad_empaque'          => $det->unidad_medida ?? 'Unid',
                        'fecha_donacion'          => $don->fecha,
                        'tipo_donacion'           => 'especie',
                        'nombre_donante'          => $don->donante?->nombre ?? 'Anónimo',
                        'almacenid'               => $almacenLocal?->almacenid,
                        'estanteid'               => $estanteLocal?->estanteid,
                        'espacioid'               => $espacioLocal?->espacioid,
                        'almacen_nombre'          => $almacen?->nombre,
                        'estante_codigo'          => $estante?->codigo_estante,
                        'espacio_codigo'          => $espacio?->codigo_espacio,
                        'fecha_ingreso_almacen'   => $ubic->fecha_ingreso,
                        'codigo_paquete'          => $paquete?->codigo_paquete,
                        'estado_paquete'          => $paquete?->estado,
                        'fecha_creacion_paquete'  => $paquete?->fecha_creacion,
                        'estado_actual'           => 'En almacén',
                        'ubicacion_actual'        => $ubicacionTexto ?: null,
                        'fecha_ultima_actualizacion' => now(),
                    ]
                );
                $count++;
            }
        });

        return $count;
    }

    public function syncDonacionesDineroFromInventario(): int
    {
        if (! $this->inventarioDisponible() || ! Schema::connection('inventario')->hasTable('donaciones_dinero')) {
            return 0;
        }

        $this->syncCampaniasFromInventario();

        $count = 0;

        DonacionesDinero::with('donacion.campana')->orderBy('id_donacion_dinero')->each(function (DonacionesDinero $dd) use (&$count) {
            $don = $dd->donacion;
            if (! $don) {
                return;
            }

            $campania = $don->id_campana
                ? Campania::where('idexterno', $don->id_campana)->first()
                : null;

            Donacion::updateOrCreate(
                ['idexterno' => $don->id_donacion],
                [
                    'usuarioid'     => null,
                    'campaniaid'    => $campania?->campaniaid,
                    'monto'         => $dd->monto ?? 0,
                    'tipodonacion'  => 'Monetaria',
                    'descripcion'   => $don->observaciones,
                    'fechadonacion' => $don->fecha ?? now(),
                    'estadoid'      => 2,
                    'esanonima'     => true,
                ]
            );
            $count++;
        });

        return $count;
    }
}
