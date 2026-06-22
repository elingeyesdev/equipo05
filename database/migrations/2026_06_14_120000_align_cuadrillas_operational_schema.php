<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'cuadrillas';

    public function up(): void
    {
        $c = $this->c;

        if (! Schema::connection($c)->hasTable('equipo')) {
            return;
        }

        Schema::connection($c)->table('equipo', function (Blueprint $table) use ($c) {
            if (! Schema::connection($c)->hasColumn('equipo', 'cantidad_integrantes')) {
                $table->integer('cantidad_integrantes')->nullable()->default(0);
            }
            if (! Schema::connection($c)->hasColumn('equipo', 'latitud')) {
                $table->decimal('latitud', 10, 8)->nullable();
            }
            if (! Schema::connection($c)->hasColumn('equipo', 'longitud')) {
                $table->decimal('longitud', 11, 8)->nullable();
            }
            if (! Schema::connection($c)->hasColumn('equipo', 'estado_id')) {
                $table->unsignedBigInteger('estado_id')->nullable();
            }
        });

        if (! Schema::connection($c)->hasTable('reporte')) {
            return;
        }

        Schema::connection($c)->table('reporte', function (Blueprint $table) use ($c) {
            $columns = [
                'nombre_reportante' => fn () => $table->string('nombre_reportante', 200)->nullable(),
                'telefono_contacto' => fn () => $table->string('telefono_contacto', 20)->nullable(),
                'fecha_hora' => fn () => $table->timestamp('fecha_hora')->nullable(),
                'nombre_lugar' => fn () => $table->string('nombre_lugar', 200)->nullable(),
                'latitud' => fn () => $table->decimal('latitud', 10, 8)->nullable(),
                'longitud' => fn () => $table->decimal('longitud', 11, 8)->nullable(),
                'tipo_incidente_id' => fn () => $table->unsignedBigInteger('tipo_incidente_id')->nullable(),
                'gravedad_id' => fn () => $table->unsignedBigInteger('gravedad_id')->nullable(),
                'comentario_adicional' => fn () => $table->text('comentario_adicional')->nullable(),
                'cant_bomberos' => fn () => $table->integer('cant_bomberos')->nullable()->default(0),
                'cant_paramedicos' => fn () => $table->integer('cant_paramedicos')->nullable()->default(0),
                'cant_veterinarios' => fn () => $table->integer('cant_veterinarios')->nullable()->default(0),
                'cant_autoridades' => fn () => $table->integer('cant_autoridades')->nullable()->default(0),
                'estado_id' => fn () => $table->unsignedBigInteger('estado_id')->nullable(),
            ];

            foreach ($columns as $name => $add) {
                if (! Schema::connection($c)->hasColumn('reporte', $name)) {
                    $add();
                }
            }
        });
    }

    public function down(): void
    {
        $c = $this->c;

        if (Schema::connection($c)->hasTable('equipo')) {
            Schema::connection($c)->table('equipo', function (Blueprint $table) use ($c) {
                foreach (['estado_id', 'longitud', 'latitud', 'cantidad_integrantes'] as $column) {
                    if (Schema::connection($c)->hasColumn('equipo', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::connection($c)->hasTable('reporte')) {
            Schema::connection($c)->table('reporte', function (Blueprint $table) use ($c) {
                foreach ([
                    'estado_id', 'cant_autoridades', 'cant_veterinarios', 'cant_paramedicos', 'cant_bomberos',
                    'comentario_adicional', 'gravedad_id', 'tipo_incidente_id', 'longitud', 'latitud',
                    'nombre_lugar', 'fecha_hora', 'telefono_contacto', 'nombre_reportante',
                ] as $column) {
                    if (Schema::connection($c)->hasColumn('reporte', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
