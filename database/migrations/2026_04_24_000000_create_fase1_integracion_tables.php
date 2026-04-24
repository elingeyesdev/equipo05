<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fase1_origenes_migracion', function (Blueprint $table) {
            $table->id();
            $table->string('modulo', 80);
            $table->string('tabla_destino', 120);
            $table->string('tabla_origen', 120);
            $table->unsignedBigInteger('id_origen');
            $table->unsignedBigInteger('id_destino')->nullable();
            $table->timestamp('migrado_en')->nullable();
            $table->string('estado', 30)->default('pendiente');
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index(['modulo', 'tabla_destino']);
            $table->index(['tabla_origen', 'id_origen']);
        });

        Schema::create('fase1_sync_log', function (Blueprint $table) {
            $table->id();
            $table->string('modulo', 80);
            $table->string('proceso', 120);
            $table->string('estado', 30)->default('pendiente');
            $table->unsignedInteger('registros_procesados')->default(0);
            $table->unsignedInteger('registros_con_error')->default(0);
            $table->text('detalle_error')->nullable();
            $table->timestamp('iniciado_en')->nullable();
            $table->timestamp('finalizado_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fase1_sync_log');
        Schema::dropIfExists('fase1_origenes_migracion');
    }
};
