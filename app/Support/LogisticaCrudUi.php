<?php

namespace App\Support;

class LogisticaCrudUi
{
    /** @var array<string, string> */
    private const LABELS = [
        'estado' => 'Estado del caso',
        'codigo_seguimiento' => 'Código de seguimiento',
        'cantidad_personas' => 'Personas afectadas',
        'fecha_inicio' => 'Inicio de emergencia',
        'fecha_necesidad' => 'Fecha límite de apoyo',
        'fecha_solicitud' => 'Fecha de solicitud',
        'tipo_emergencia' => 'Tipo de emergencia',
        'insumos_necesarios' => 'Insumos necesarios',
        'id_solicitante' => 'Solicitante',
        'id_destino' => 'Destino',
        'aprobada' => 'Solicitud aprobada',
        'apoyoaceptado' => 'Apoyo aceptado por comunidad',
        'codigo' => 'Código del paquete',
        'ubicacion_actual' => 'Ubicación actual',
        'fecha_creacion' => 'Fecha de armado',
        'fecha_entrega' => 'Fecha de entrega',
        'estado_id' => 'Estado del paquete',
        'id_solicitud' => 'Solicitud vinculada',
        'placa' => 'Placa',
        'modelo' => 'Modelo',
        'capacidad' => 'Capacidad de carga',
        'id_marca' => 'Marca',
        'id_tipovehiculo' => 'Tipo de vehículo',
        'nombre' => 'Nombre',
        'apellido' => 'Apellido',
        'ci' => 'Cédula de identidad',
        'telefono' => 'Teléfono',
        'email' => 'Correo electrónico',
        'comunidad' => 'Comunidad',
        'provincia' => 'Provincia',
        'direccion' => 'Dirección',
        'nombre_estado' => 'Nombre del estado',
        'nombre_marca' => 'Nombre de marca',
        'nombre_tipovehiculo' => 'Tipo de vehículo',
        'tipo_licencia' => 'Tipo de licencia',
        'tipo_emergencia_catalogo' => 'Tipo de emergencia',
        'descripcion' => 'Descripción',
        'zona' => 'Zona / referencia',
        'latitud' => 'Latitud',
        'longitud' => 'Longitud',
        'titulo' => 'Título',
        'vehiculo_placa' => 'Placa del vehículo',
        'conductor_nombre' => 'Conductor',
        'conductor_ci' => 'CI del conductor',
        'fecha_actualizacion' => 'Fecha de actualización',
    ];

    /** @var array<string, list<string>> */
    private const COLUMN_ORDER = [
        'solicitud' => [
            'estado', 'codigo_seguimiento', 'fecha_solicitud',
            'tipo_emergencia', 'cantidad_personas', 'fecha_inicio', 'fecha_necesidad',
            'id_solicitante', 'id_destino',
            'insumos_necesarios',
            'aprobada', 'apoyoaceptado',
        ],
        'paquete' => [
            'codigo', 'estado_id', 'id_solicitud',
            'ubicacion_actual', 'fecha_creacion', 'fecha_entrega',
        ],
        'vehiculo' => ['placa', 'modelo', 'capacidad', 'id_marca', 'id_tipovehiculo'],
        'conductor' => ['nombre', 'apellido', 'ci', 'telefono', 'id_licencia'],
        'seguimiento' => ['id_paquete', 'estado', 'fecha_actualizacion', 'vehiculo_placa', 'conductor_nombre', 'conductor_ci'],
    ];

    /** @var array<string, list<string>> */
    private const CONFIG_SECTIONS = [
        'solicitante', 'destino', 'ubicacion', 'marca', 'tipo-vehiculo',
        'usuario', 'rol', 'estado', 'tipo-emergencia', 'tipo-licencia', 'reporte',
    ];

    public static function label(string $column): string
    {
        return self::LABELS[$column] ?? ucwords(str_replace('_', ' ', $column));
    }

    /** @param  list<string>  $columns */
    public static function orderColumns(string $seccion, array $columns): array
    {
        $preferred = self::COLUMN_ORDER[$seccion] ?? [];
        $ordered = [];

        foreach ($preferred as $column) {
            if (in_array($column, $columns, true)) {
                $ordered[] = $column;
            }
        }

        foreach ($columns as $column) {
            if (! in_array($column, $ordered, true)) {
                $ordered[] = $column;
            }
        }

        return $ordered;
    }

    public static function colClass(string $seccion, string $column): string
    {
        if (in_array($column, ['insumos_necesarios', 'descripcion', 'contenido', 'observacion'], true)) {
            return 'col-12';
        }

        if ($seccion === 'solicitud' && $column === 'codigo_seguimiento') {
            return 'col-md-6';
        }

        return 'col-md-4 col-sm-6';
    }

    public static function isReadonly(string $seccion, string $column): bool
    {
        return $seccion === 'solicitud' && $column === 'codigo_seguimiento';
    }

    public static function isBooleanField(string $column): bool
    {
        return in_array($column, ['aprobada', 'apoyoaceptado', 'activo', 'administrador', 'usado'], true);
    }

    public static function sectionTitle(string $seccion, string $column): ?string
    {
        return match ($seccion) {
            'solicitud' => match ($column) {
                'estado', 'codigo_seguimiento', 'fecha_solicitud' => 'Estado del caso',
                'tipo_emergencia', 'cantidad_personas', 'fecha_inicio', 'fecha_necesidad', 'insumos_necesarios' => 'Datos de la emergencia',
                'id_solicitante', 'id_destino' => 'Solicitante y destino',
                'aprobada', 'apoyoaceptado' => 'Gestión interna',
                default => null,
            },
            'paquete' => match ($column) {
                'codigo', 'estado_id', 'id_solicitud' => 'Identificación',
                'ubicacion_actual', 'fecha_creacion', 'fecha_entrega' => 'Seguimiento operativo',
                default => null,
            },
            default => null,
        };
    }

    public static function listRouteName(string $seccion): string
    {
        return match ($seccion) {
            'solicitud' => 'logistica.solicitud',
            'paquete' => 'logistica.paquete',
            'vehiculo', 'conductor' => 'logistica.flota',
            default => "logistica.{$seccion}",
        };
    }

    /** @return array<string, mixed> */
    public static function listRouteParams(string $seccion): array
    {
        return match ($seccion) {
            'vehiculo' => ['tab' => 'vehiculos'],
            'conductor' => ['tab' => 'conductores'],
            default => [],
        };
    }

    public static function isConfigSection(string $seccion): bool
    {
        return in_array($seccion, self::CONFIG_SECTIONS, true);
    }

    /** @return list<string> */
    public static function estadoSolicitudOptions(): array
    {
        return ['pendiente', 'aprobada', 'en_ruta', 'entregada', 'rechazada', 'negada'];
    }

    /** @return list<string> */
    public static function emergenciaFallbackOptions(): array
    {
        return ['Inundación', 'Sequía', 'Incendio', 'Granizada', 'Deslizamiento', 'Otra'];
    }
}
