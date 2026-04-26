<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            // Eliminar campos que ya no se usan
            if (Schema::hasColumn('biomasas', 'nombre')) {
                $table->dropColumn('nombre');
            }
            if (Schema::hasColumn('biomasas', 'humedad')) {
                $table->dropColumn('humedad');
            }
            if (Schema::hasColumn('biomasas', 'tipo')) {
                $table->dropColumn('tipo');
            }
            
            // Agregar nuevos campos
            if (!Schema::hasColumn('biomasas', 'fecha_reporte')) {
                $table->date('fecha_reporte')->nullable()->after('id');
            }
            if (!Schema::hasColumn('biomasas', 'coordenadas')) {
                $table->json('coordenadas')->nullable()->after('ubicacion');
            }
            
            // Modificar densidad para que sea string (baja/media/alta)
            if (Schema::hasColumn('biomasas', 'densidad')) {
                $table->string('densidad')->default('media')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            // Restaurar campos eliminados
            $table->string('nombre')->nullable();
            $table->float('humedad')->nullable();
            $table->string('tipo')->nullable();
            
            // Eliminar campos nuevos
            if (Schema::hasColumn('biomasas', 'fecha_reporte')) {
                $table->dropColumn('fecha_reporte');
            }
            if (Schema::hasColumn('biomasas', 'coordenadas')) {
                $table->dropColumn('coordenadas');
            }
            
            // Restaurar densidad a float
            $table->float('densidad')->nullable()->change();
        });
    }
};
