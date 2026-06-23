<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'logistica';

    public function up(): void
    {
        $schema = Schema::connection($this->c);

        if ($schema->hasTable('paquete') && ! $schema->hasColumn('paquete', 'imagen')) {
            $schema->table('paquete', function (Blueprint $table) {
                $table->binary('imagen')->nullable();
            });
        }

        if ($schema->hasTable('vehiculo')) {
            $schema->table('vehiculo', function (Blueprint $table) use ($schema) {
                if (! $schema->hasColumn('vehiculo', 'modelo')) {
                    $table->string('modelo', 120)->nullable()->after('placa');
                }
                if (! $schema->hasColumn('vehiculo', 'anio')) {
                    $table->unsignedSmallInteger('anio')->nullable()->after('modelo');
                }
                if (! $schema->hasColumn('vehiculo', 'capacidad')) {
                    $table->string('capacidad', 64)->nullable()->after('anio');
                }
                if (! $schema->hasColumn('vehiculo', 'id_marca')) {
                    $table->unsignedBigInteger('id_marca')->nullable()->after('capacidad');
                }
                if (! $schema->hasColumn('vehiculo', 'id_tipovehiculo')
                    && ! $schema->hasColumn('vehiculo', 'id_tipo_vehiculo')) {
                    $table->unsignedBigInteger('id_tipovehiculo')->nullable()->after('id_marca');
                }
                if (! $schema->hasColumn('vehiculo', 'observaciones')) {
                    $table->string('observaciones', 255)->nullable();
                }
            });
        }

        if ($schema->hasTable('conductor')) {
            $schema->table('conductor', function (Blueprint $table) use ($schema) {
                if (! $schema->hasColumn('conductor', 'ci')) {
                    $table->string('ci', 40)->nullable()->after('apellido');
                }
                if (! $schema->hasColumn('conductor', 'telefono')) {
                    $table->string('telefono', 40)->nullable()->after('ci');
                }
                if (! $schema->hasColumn('conductor', 'email')) {
                    $table->string('email', 120)->nullable()->after('telefono');
                }
                if (! $schema->hasColumn('conductor', 'id_licencia')) {
                    $table->unsignedBigInteger('id_licencia')->nullable()->after('email');
                }
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection($this->c);

        if ($schema->hasTable('paquete') && $schema->hasColumn('paquete', 'imagen')) {
            $schema->table('paquete', fn (Blueprint $table) => $table->dropColumn('imagen'));
        }

        if ($schema->hasTable('vehiculo')) {
            $schema->table('vehiculo', function (Blueprint $table) use ($schema) {
                foreach (['observaciones', 'id_tipovehiculo', 'id_tipo_vehiculo', 'id_marca', 'capacidad', 'anio', 'modelo'] as $col) {
                    if ($schema->hasColumn('vehiculo', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if ($schema->hasTable('conductor')) {
            $schema->table('conductor', function (Blueprint $table) use ($schema) {
                foreach (['id_licencia', 'email', 'telefono', 'ci'] as $col) {
                    if ($schema->hasColumn('conductor', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
