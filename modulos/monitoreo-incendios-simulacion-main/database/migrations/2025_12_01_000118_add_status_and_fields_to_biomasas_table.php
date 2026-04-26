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
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('biomasas', 'fecha_reporte')) {
                $table->date('fecha_reporte')->nullable();
            }
            if (!Schema::hasColumn('biomasas', 'tipo_biomasa_id')) {
                $table->foreignId('tipo_biomasa_id')->nullable()->constrained('tipo_biomasas')->onDelete('set null');
            }
            if (!Schema::hasColumn('biomasas', 'area_m2')) {
                $table->decimal('area_m2', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('biomasas', 'perimetro_m')) {
                $table->decimal('perimetro_m', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('biomasas', 'densidad')) {
                $table->enum('densidad', ['Baja', 'Media', 'Alta'])->default('Media');
            }
            if (!Schema::hasColumn('biomasas', 'ubicacion')) {
                $table->string('ubicacion')->nullable();
            }
            if (!Schema::hasColumn('biomasas', 'coordenadas')) {
                $table->json('coordenadas')->nullable();
            }
            if (!Schema::hasColumn('biomasas', 'descripcion')) {
                $table->text('descripcion')->nullable();
            }
            if (!Schema::hasColumn('biomasas', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            
            // Nuevo campo para moderación
            if (!Schema::hasColumn('biomasas', 'estado')) {
                $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            }
            if (!Schema::hasColumn('biomasas', 'motivo_rechazo')) {
                $table->text('motivo_rechazo')->nullable();
            }
            if (!Schema::hasColumn('biomasas', 'aprobada_por')) {
                $table->foreignId('aprobada_por')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('biomasas', 'fecha_revision')) {
                $table->timestamp('fecha_revision')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            $table->dropColumn([
                'estado',
                'motivo_rechazo',
                'aprobada_por',
                'fecha_revision'
            ]);
        });
    }
};
