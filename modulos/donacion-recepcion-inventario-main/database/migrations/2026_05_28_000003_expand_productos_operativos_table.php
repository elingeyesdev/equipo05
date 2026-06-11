<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('codigo', 50)->nullable()->unique()->after('id_categoria');
            $table->string('imagen_url', 500)->nullable()->after('descripcion');
            $table->string('prioridad', 10)->default('media')->after('unidad_medida');
            $table->string('estado', 20)->default('activo')->after('prioridad');
            $table->boolean('requiere_vencimiento')->default(false)->after('estado');
            $table->boolean('requiere_talla')->default(false)->after('requiere_vencimiento');
            $table->boolean('requiere_condicion')->default(false)->after('requiere_talla');
            $table->boolean('producto_restringido')->default(false)->after('requiere_condicion');
            $table->unsignedInteger('stock_minimo')->nullable()->default(0)->after('producto_restringido');
            $table->text('condiciones_almacenamiento')->nullable()->after('stock_minimo');
            $table->text('observaciones')->nullable()->after('condiciones_almacenamiento');
        });

        $categorias = DB::table('categorias_productos')->get()->keyBy('id_categoria');
        $usedCodes = [];

        foreach (DB::table('productos')->orderBy('id_producto')->get() as $row) {
            $cat = $categorias->get($row->id_categoria);
            $codigo = $this->generarCodigoUnico($row->nombre, $row->id_producto, $usedCodes);
            $usedCodes[] = $codigo;

            $update = [
                'codigo' => $codigo,
                'prioridad' => $cat->prioridad ?? 'media',
                'estado' => 'activo',
                'stock_minimo' => 0,
            ];

            if ($cat) {
                $update['requiere_vencimiento'] = (bool) $cat->requiere_fecha_vencimiento;
                $update['requiere_talla'] = $cat->tipo_categoria === 'VESTIMENTA';
                $update['producto_restringido'] = $cat->tipo_categoria === 'SALUD';
                $update['condiciones_almacenamiento'] = $cat->condiciones_almacenamiento;
                if (! $row->unidad_medida && $cat->unidad_medida) {
                    $update['unidad_medida'] = $cat->unidad_medida;
                }
            }

            DB::table('productos')->where('id_producto', $row->id_producto)->update($update);
        }
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'codigo',
                'imagen_url',
                'prioridad',
                'estado',
                'requiere_vencimiento',
                'requiere_talla',
                'requiere_condicion',
                'producto_restringido',
                'stock_minimo',
                'condiciones_almacenamiento',
                'observaciones',
            ]);
        });
    }

    private function generarCodigoUnico(string $nombre, int $id, array $usedCodes): string
    {
        $base = 'PROD-'.strtoupper(trim(preg_replace('/[^A-Z0-9]+/', '-', strtoupper($nombre)) ?: 'ITEM', '-'));
        $base = substr($base, 0, 40) ?: 'PROD-ITEM';
        $codigo = $base.'-'.str_pad((string) $id, 3, '0', STR_PAD_LEFT);

        $suffix = 1;
        while (in_array($codigo, $usedCodes, true)) {
            $codigo = $base.'-'.str_pad((string) ($id + $suffix), 3, '0', STR_PAD_LEFT);
            $suffix++;
        }

        return $codigo;
    }
};
