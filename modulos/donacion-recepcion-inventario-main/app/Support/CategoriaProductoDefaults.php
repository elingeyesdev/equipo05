<?php

namespace Modules\Inventario\Support;

class CategoriaProductoDefaults
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function catalogoEmergencia(): array
    {
        return [
            [
                'nombre' => 'Agua potable',
                'codigo' => 'CAT-AGUA',
                'descripcion' => 'Agua embotellada o en bidones apta para consumo humano.',
                'tipo_categoria' => 'CONSUMO',
                'unidad_medida' => 'litros',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'alta',
                'condiciones_almacenamiento' => 'Lugar seco, fresco, protegido del sol directo.',
                'recomendaciones_uso' => 'Recurso crítico para damnificados y brigadistas. Priorizar distribución inmediata.',
                'observaciones' => 'Controlar cantidad disponible por centro de acopio.',
                'color' => '#007bff',
                'icono' => 'fas fa-tint',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Alimentos no perecederos',
                'codigo' => 'CAT-ALIM-NP',
                'descripcion' => 'Arroz, fideos, enlatados, galletas, azúcar y similares de larga duración.',
                'tipo_categoria' => 'CONSUMO',
                'unidad_medida' => 'kilos',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'alta',
                'condiciones_almacenamiento' => 'Ambiente seco y ventilado, elevado del piso.',
                'recomendaciones_uso' => 'Base para canastas de alimentación en campo. Combinar con agua potable.',
                'observaciones' => null,
                'color' => '#28a745',
                'icono' => 'fas fa-box',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Alimentos perecederos',
                'codigo' => 'CAT-ALIM-PER',
                'descripcion' => 'Productos frescos o con caducidad corta que requieren control de vencimiento.',
                'tipo_categoria' => 'CONSUMO',
                'unidad_medida' => 'kilos',
                'es_perecedero' => true,
                'requiere_fecha_vencimiento' => true,
                'prioridad' => 'alta',
                'condiciones_almacenamiento' => 'Refrigerado o ambiente fresco y ventilado según producto.',
                'recomendaciones_uso' => 'Distribuir antes del vencimiento. Alertar si queda poco tiempo útil.',
                'observaciones' => 'Evitar acumular sin fecha de caducidad registrada.',
                'color' => '#fd7e14',
                'icono' => 'fas fa-utensils',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Medicamentos básicos',
                'codigo' => 'CAT-MEDI',
                'descripcion' => 'Analgésicos, antisépticos, vendas y medicamentos de primeros auxilios.',
                'tipo_categoria' => 'SALUD',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => true,
                'prioridad' => 'alta',
                'condiciones_almacenamiento' => 'Lugar seco, temperatura controlada, lejos de luz directa.',
                'recomendaciones_uso' => 'Requiere control y personal autorizado para su entrega.',
                'observaciones' => 'Verificar caducidad antes de despachar.',
                'color' => '#dc3545',
                'icono' => 'fas fa-pills',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Productos de higiene',
                'codigo' => 'CAT-HIGI',
                'descripcion' => 'Jabón, papel higiénico, alcohol, pañales y artículos de aseo.',
                'tipo_categoria' => 'HIGIENE',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'media',
                'condiciones_almacenamiento' => 'Ambiente seco.',
                'recomendaciones_uso' => 'Incluir en kits de apoyo a familias damnificadas.',
                'observaciones' => null,
                'color' => '#17a2b8',
                'icono' => 'fas fa-soap',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Ropa y abrigo',
                'codigo' => 'CAT-ROPA',
                'descripcion' => 'Chamarras, frazadas, ropa limpia y elementos de abrigo.',
                'tipo_categoria' => 'VESTIMENTA',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'media',
                'condiciones_almacenamiento' => 'Ambiente seco, protegido de humedad.',
                'recomendaciones_uso' => 'Registrar tallas al recibir donaciones de vestimenta.',
                'observaciones' => null,
                'color' => '#6f42c1',
                'icono' => 'fas fa-tshirt',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Herramientas',
                'codigo' => 'CAT-HERR',
                'descripcion' => 'Palas, machetes, linternas, guantes y utensilios de trabajo.',
                'tipo_categoria' => 'HERRAMIENTA',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'alta',
                'condiciones_almacenamiento' => 'Ambiente seco, herramientas con filo protegidas.',
                'recomendaciones_uso' => 'Asignar a brigadas de limpieza y contención.',
                'observaciones' => null,
                'color' => '#343a40',
                'icono' => 'fas fa-tools',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Equipos de protección',
                'codigo' => 'CAT-EPP',
                'descripcion' => 'Mascarillas, cascos, botas, lentes y equipo de protección personal.',
                'tipo_categoria' => 'RESCATE',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'alta',
                'condiciones_almacenamiento' => 'Lugar seco, sin exposición a químicos.',
                'recomendaciones_uso' => 'Priorizar brigadistas y personal en zona de riesgo.',
                'observaciones' => null,
                'color' => '#ffc107',
                'icono' => 'fas fa-hard-hat',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Alimento para animales',
                'codigo' => 'CAT-ANIM',
                'descripcion' => 'Alimento para mascotas y ganado en zonas rurales afectadas.',
                'tipo_categoria' => 'CONSUMO',
                'unidad_medida' => 'kilos',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'media',
                'condiciones_almacenamiento' => 'Ambiente seco, protegido de roedores.',
                'recomendaciones_uso' => 'Útil en emergencias con afectación ganadera.',
                'observaciones' => null,
                'color' => '#795548',
                'icono' => 'fas fa-paw',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Otros',
                'codigo' => 'CAT-OTRO',
                'descripcion' => 'Productos donados que no encajan en las categorías principales.',
                'tipo_categoria' => 'OTRO',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 'baja',
                'condiciones_almacenamiento' => 'Según naturaleza del producto.',
                'recomendaciones_uso' => 'Reclasificar cuando sea posible a una categoría específica.',
                'observaciones' => 'Categoría temporal de captura.',
                'color' => '#6c757d',
                'icono' => 'fas fa-box-open',
                'estado' => 'activo',
            ],
        ];
    }

    public static function mapaLegacyPorNombre(): array
    {
        return [
            'Agua' => 'CAT-AGUA',
            'Agua potable' => 'CAT-AGUA',
            'Alimentos' => 'CAT-ALIM-PER',
            'Alimentos no perecederos' => 'CAT-ALIM-NP',
            'Alimentos perecederos' => 'CAT-ALIM-PER',
            'Medicamentos' => 'CAT-MEDI',
            'Medicamentos básicos' => 'CAT-MEDI',
            'Higiene' => 'CAT-HIGI',
            'Productos de higiene' => 'CAT-HIGI',
            'Ropa' => 'CAT-ROPA',
            'Ropa y abrigo' => 'CAT-ROPA',
            'Herramientas' => 'CAT-HERR',
            'Equipos de protección' => 'CAT-EPP',
            'Alimento para animales' => 'CAT-ANIM',
            'Otros' => 'CAT-OTRO',
        ];
    }

    public static function rowFor(string $nombre): array
    {
        foreach (self::catalogoEmergencia() as $item) {
            if ($item['nombre'] === $nombre) {
                return $item;
            }
        }

        $codigo = 'CAT-'.strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $nombre) ?: 'OTRO', 0, 12));

        return [
            'nombre' => $nombre,
            'codigo' => $codigo,
            'tipo_categoria' => 'OTRO',
            'es_perecedero' => false,
            'requiere_fecha_vencimiento' => false,
            'prioridad' => 'baja',
            'estado' => 'activo',
        ];
    }
}
