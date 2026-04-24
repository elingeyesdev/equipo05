<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_recoleccion', function (Blueprint $table) {
            $table->increments('id_solicitud');
            $table->unsignedInteger('id_donante');
            $table->unsignedInteger('id_recolector')->nullable();
            $table->text('direccion_recoleccion');
            $table->timestamp('fecha_programada');
            $table->text('observaciones')->nullable();
            $table->string('estado', 30)->default('pendiente');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->foreign('id_donante')->references('id_donante')->on('donantes')->onDelete('cascade');
            $table->foreign('id_recolector')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_recoleccion');
    }
};



