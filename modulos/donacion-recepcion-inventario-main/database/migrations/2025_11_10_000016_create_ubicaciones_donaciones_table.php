<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubicaciones_donaciones', function (Blueprint $table) {
            $table->increments('id_ubicacion');
            $table->unsignedInteger('id_detalle')->nullable();
            $table->unsignedInteger('id_espacio')->nullable();
            $table->timestamp('fecha_ingreso')->useCurrent();
            $table->integer('cantidad_ubicada')->default(1);

            $table->foreign('id_detalle')->references('id_detalle')->on('donacion_detalles')->onDelete('cascade');
            $table->foreign('id_espacio')->references('id_espacio')->on('espacios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubicaciones_donaciones');
    }
};



