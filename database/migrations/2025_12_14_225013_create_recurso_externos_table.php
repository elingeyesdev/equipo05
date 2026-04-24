<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_recursos_externos_table.php
    public function up(): void
    {
        Schema::create('recursos_externos', function (Blueprint $table) {
            $table->id();
            
            // Tipo de recurso: 'solicitud', 'vehiculo', 'especie'
            $table->string('tipo')->index(); 
            
            // El ID original que viene del Gateway (ej: id_solicitud, id_vehiculo)
            $table->unsignedBigInteger('gateway_id'); 
            
            // El dato principal visible (ej: "GRT-8116", "4696SSN", "Ave")
            $table->string('identificador')->index(); 
            
            // Aquí guardamos el resto (ej: estado: "pendiente") en formato JSON
            $table->json('datos_extra')->nullable(); 
            
            $table->timestamps();

            // Evita duplicados: No puedes tener dos solicitudes con el mismo ID
            $table->unique(['tipo', 'gateway_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurso_externos');
    }
};
