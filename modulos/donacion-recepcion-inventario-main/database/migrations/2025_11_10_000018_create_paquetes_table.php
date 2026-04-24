<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paquetes', function (Blueprint $table) {
            $table->increments('id_paquete');
            $table->string('codigo_paquete', 50)->unique()->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->unsignedInteger('id_solicitud')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->string('codigo_solicitud_externa', 100)->nullable();

            $table->foreign('id_solicitud')->references('id_solicitud')->on('solicitudes_ayuda')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paquetes');
    }
};



