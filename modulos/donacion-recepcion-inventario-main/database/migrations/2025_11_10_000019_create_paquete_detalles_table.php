<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paquete_detalles', function (Blueprint $table) {
            $table->increments('id_detalle');
            $table->unsignedInteger('id_paquete')->nullable();
            $table->unsignedInteger('id_detalle_donacion')->nullable();
            $table->integer('cantidad_usada')->default(0);

            $table->foreign('id_paquete')->references('id_paquete')->on('paquetes')->onDelete('cascade');
            $table->foreign('id_detalle_donacion')->references('id_detalle')->on('donacion_detalles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paquete_detalles');
    }
};



