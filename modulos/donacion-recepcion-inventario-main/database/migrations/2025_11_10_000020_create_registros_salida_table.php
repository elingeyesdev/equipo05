<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_salida', function (Blueprint $table) {
            $table->increments('id_salida');
            $table->unsignedInteger('id_paquete')->nullable();
            $table->timestamp('fecha_salida')->useCurrent();
            $table->text('destino')->nullable();
            $table->text('observaciones')->nullable();

            $table->foreign('id_paquete')->references('id_paquete')->on('paquetes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_salida');
    }
};



