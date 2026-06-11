<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias_productos', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->unique()->after('nombre');
            $table->text('descripcion')->nullable()->after('codigo');
            $table->string('tipo_categoria', 30)->default('OTRO')->after('descripcion');
            $table->string('unidad_medida', 50)->nullable()->after('tipo_categoria');
            $table->boolean('es_perecedero')->default(false)->after('unidad_medida');
            $table->boolean('requiere_fecha_vencimiento')->default(false)->after('es_perecedero');
            $table->unsignedSmallInteger('prioridad')->default(50)->after('requiere_fecha_vencimiento');
            $table->text('condiciones_almacenamiento')->nullable()->after('prioridad');
            $table->string('color', 20)->nullable()->after('condiciones_almacenamiento');
            $table->string('icono', 80)->nullable()->after('color');
            $table->string('estado', 20)->default('activo')->after('icono');
            $table->timestamps();
        });

        $now = now();
        $presets = [
            'Alimentos' => [
                'codigo' => 'ALIM',
                'tipo_categoria' => 'ALIMENTO',
                'unidad_medida' => 'kg',
                'es_perecedero' => true,
                'requiere_fecha_vencimiento' => true,
                'prioridad' => 10,
                'condiciones_almacenamiento' => 'Lugar seco, fresco y ventilado',
                'color' => '#28a745',
                'icono' => 'fas fa-utensils',
            ],
            'Ropa' => [
                'codigo' => 'ROPA',
                'tipo_categoria' => 'ROPA',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 30,
                'condiciones_almacenamiento' => 'Ambiente seco, protegido de humedad',
                'color' => '#6f42c1',
                'icono' => 'fas fa-tshirt',
            ],
            'Higiene' => [
                'codigo' => 'HIGI',
                'tipo_categoria' => 'HIGIENE',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 20,
                'condiciones_almacenamiento' => 'Ambiente seco',
                'color' => '#17a2b8',
                'icono' => 'fas fa-soap',
            ],
            'Medicamentos' => [
                'codigo' => 'MEDI',
                'tipo_categoria' => 'MEDICINA',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => true,
                'prioridad' => 5,
                'condiciones_almacenamiento' => 'Temperatura controlada, lejos de luz directa',
                'color' => '#dc3545',
                'icono' => 'fas fa-pills',
            ],
            'Herramientas' => [
                'codigo' => 'HERR',
                'tipo_categoria' => 'HERRAMIENTA',
                'unidad_medida' => 'unidades',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 40,
                'condiciones_almacenamiento' => 'Ambiente seco',
                'color' => '#fd7e14',
                'icono' => 'fas fa-tools',
            ],
            'Agua' => [
                'codigo' => 'AGUA',
                'tipo_categoria' => 'AGUA',
                'unidad_medida' => 'litros',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 8,
                'condiciones_almacenamiento' => 'Lugar fresco, alejado del sol',
                'color' => '#007bff',
                'icono' => 'fas fa-tint',
            ],
        ];

        $rows = DB::table('categorias_productos')->get();
        $usedCodes = [];

        foreach ($rows as $row) {
            $preset = $presets[$row->nombre] ?? null;
            $codigo = $preset['codigo'] ?? $this->generateCodigo($row->nombre, $usedCodes);
            $usedCodes[] = $codigo;

            $update = [
                'codigo' => $codigo,
                'tipo_categoria' => 'OTRO',
                'es_perecedero' => false,
                'requiere_fecha_vencimiento' => false,
                'prioridad' => 50,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($preset) {
                $update = array_merge($update, $preset);
                $update['codigo'] = $codigo;
            }

            DB::table('categorias_productos')->where('id_categoria', $row->id_categoria)->update($update);
        }
    }

    public function down(): void
    {
        Schema::table('categorias_productos', function (Blueprint $table) {
            $table->dropColumn([
                'codigo',
                'descripcion',
                'tipo_categoria',
                'unidad_medida',
                'es_perecedero',
                'requiere_fecha_vencimiento',
                'prioridad',
                'condiciones_almacenamiento',
                'color',
                'icono',
                'estado',
                'created_at',
                'updated_at',
            ]);
        });
    }

    private function generateCodigo(string $nombre, array $usedCodes): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $nombre) ?: 'CAT', 0, 8));
        $codigo = $base;
        $suffix = 1;

        while (in_array($codigo, $usedCodes, true)) {
            $codigo = substr($base, 0, 6).$suffix;
            $suffix++;
        }

        return $codigo;
    }
};
