<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ext_paquetes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_paquete')->unique(); // PKG-2025...
            $table->string('estado')->nullable();       // enviado, entregado...
            $table->timestamp('fecha_creacion')->nullable();
            
            // Aquí guardamos todo el JSON del gateway (salidas, logística, etc.)
            // PostgreSQL maneja esto de maravilla.
            $table->json('datos_gateway')->nullable(); 
            
            $table->timestamp('ultimo_sync')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ext_paquetes');
    }
};
