<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Inventario\Support\CategoriaProductoDefaults;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias_productos', function (Blueprint $table) {
            if (! Schema::hasColumn('categorias_productos', 'recomendaciones_uso')) {
                $table->text('recomendaciones_uso')->nullable()->after('condiciones_almacenamiento');
            }
            if (! Schema::hasColumn('categorias_productos', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('recomendaciones_uso');
            }
        });

        if (Schema::hasColumn('categorias_productos', 'prioridad')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE categorias_productos ALTER COLUMN prioridad TYPE VARCHAR(10) USING (
                    CASE
                        WHEN prioridad::text ~ '^[0-9]+$' AND prioridad::int <= 15 THEN 'alta'
                        WHEN prioridad::text ~ '^[0-9]+$' AND prioridad::int <= 35 THEN 'media'
                        WHEN prioridad::text IN ('alta','media','baja') THEN prioridad::text
                        ELSE 'baja'
                    END
                )");
                DB::statement("ALTER TABLE categorias_productos ALTER COLUMN prioridad SET DEFAULT 'media'");
            } else {
                Schema::table('categorias_productos', function (Blueprint $table) {
                    $table->string('prioridad_emergencia', 10)->default('media')->after('requiere_fecha_vencimiento');
                });

                foreach (DB::table('categorias_productos')->get() as $row) {
                    $prioridad = is_numeric($row->prioridad)
                        ? ((int) $row->prioridad <= 15 ? 'alta' : ((int) $row->prioridad <= 35 ? 'media' : 'baja'))
                        : (in_array($row->prioridad, ['alta', 'media', 'baja'], true) ? $row->prioridad : 'media');

                    DB::table('categorias_productos')->where('id_categoria', $row->id_categoria)->update([
                        'prioridad_emergencia' => $prioridad,
                    ]);
                }

                Schema::table('categorias_productos', function (Blueprint $table) {
                    $table->dropColumn('prioridad');
                });

                Schema::table('categorias_productos', function (Blueprint $table) {
                    $table->renameColumn('prioridad_emergencia', 'prioridad');
                });
            }
        }

        $mapaTipos = [
            'ALIMENTO' => 'CONSUMO',
            'AGUA' => 'CONSUMO',
            'MEDICINA' => 'SALUD',
            'ROPA' => 'VESTIMENTA',
        ];

        foreach ($mapaTipos as $viejo => $nuevo) {
            DB::table('categorias_productos')->where('tipo_categoria', $viejo)->update(['tipo_categoria' => $nuevo]);
        }

        if (! Schema::hasTable('categorias_productos_historial')) {
            Schema::create('categorias_productos_historial', function (Blueprint $table) {
                $table->bigIncrements('id_historial');
                $table->unsignedInteger('id_categoria');
                $table->string('accion', 30);
                $table->string('usuario_ci', 30)->nullable();
                $table->json('datos_anteriores')->nullable();
                $table->json('datos_nuevos')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('id_categoria')
                    ->references('id_categoria')
                    ->on('categorias_productos')
                    ->onDelete('cascade');
            });
        }

        $now = now();
        $idsPorCodigo = [];
        $legacyMap = [];

        foreach (DB::table('categorias_productos')->get() as $row) {
            $legacyMap[$row->id_categoria] = $row->nombre;
        }

        foreach (CategoriaProductoDefaults::catalogoEmergencia() as $item) {
            $existente = DB::table('categorias_productos')->where('codigo', $item['codigo'])->first();

            $payload = array_merge($item, ['updated_at' => $now]);

            if ($existente) {
                DB::table('categorias_productos')->where('id_categoria', $existente->id_categoria)->update($payload);
                $idsPorCodigo[$item['codigo']] = $existente->id_categoria;
            } else {
                $payload['created_at'] = $now;
                $idsPorCodigo[$item['codigo']] = DB::table('categorias_productos')->insertGetId($payload, 'id_categoria');
            }
        }

        $nombreACodigo = CategoriaProductoDefaults::mapaLegacyPorNombre();

        $catalogCodes = collect(CategoriaProductoDefaults::catalogoEmergencia())->pluck('codigo')->all();

        foreach ($legacyMap as $oldId => $nombre) {
            $codigo = $nombreACodigo[$nombre] ?? null;
            if (! $codigo || ! isset($idsPorCodigo[$codigo])) {
                continue;
            }

            $newId = $idsPorCodigo[$codigo];
            if ($oldId === $newId) {
                continue;
            }

            DB::table('productos')->where('id_categoria', $oldId)->update(['id_categoria' => $newId]);

            $oldCodigo = DB::table('categorias_productos')->where('id_categoria', $oldId)->value('codigo');
            if ($oldCodigo && ! in_array($oldCodigo, $catalogCodes, true)) {
                DB::table('categorias_productos')->where('id_categoria', $oldId)->delete();
            }
        }

        foreach (CategoriaProductoDefaults::catalogoEmergencia() as $item) {
            if (! DB::table('categorias_productos')->where('codigo', $item['codigo'])->exists()) {
                $row = $item;
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                DB::table('categorias_productos')->insert($row);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_productos_historial');

        Schema::table('categorias_productos', function (Blueprint $table) {
            if (Schema::hasColumn('categorias_productos', 'recomendaciones_uso')) {
                $table->dropColumn('recomendaciones_uso');
            }
            if (Schema::hasColumn('categorias_productos', 'observaciones')) {
                $table->dropColumn('observaciones');
            }
        });
    }
};
