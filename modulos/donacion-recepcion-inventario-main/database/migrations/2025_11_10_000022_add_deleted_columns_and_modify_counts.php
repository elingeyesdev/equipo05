<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // add deleted_at and deleted_by columns where requested
        Schema::table('donaciones', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('observaciones');
            $table->unsignedInteger('deleted_by')->nullable()->after('deleted_at');
        });

        Schema::table('paquetes', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('estado');
            $table->unsignedInteger('deleted_by')->nullable()->after('deleted_at');
        });

        Schema::table('donantes', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('fecha_registro');
            $table->unsignedInteger('deleted_by')->nullable()->after('deleted_at');
        });

        // modify cantidad columns types in donacion_detalles and paquete_detalles
        if (Schema::hasTable('donacion_detalles')) {
            Schema::table('donacion_detalles', function (Blueprint $table) {
                // ensure cantidad and cantidad_por_unidad are integers
                $table->integer('cantidad')->change();
                if (Schema::hasColumn('donacion_detalles', 'cantidad_por_unidad')) {
                    $table->integer('cantidad_por_unidad')->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('paquete_detalles')) {
            Schema::table('paquete_detalles', function (Blueprint $table) {
                $table->integer('cantidad_usada')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('donaciones', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleted_by']);
        });
        Schema::table('paquetes', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleted_by']);
        });
        Schema::table('donantes', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleted_by']);
        });
    }
};



