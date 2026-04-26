<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Añade campos necesarios para la simulación avanzada de incendios
     */
    public function up(): void
    {
        // Añadir campos a simulaciones para parámetros ambientales y configuración
        Schema::table('simulaciones', function (Blueprint $table) {
            // Parámetros ambientales
            $table->decimal('temperature', 5, 2)->nullable()->after('estado')->comment('Temperatura en °C');
            $table->decimal('humidity', 5, 2)->nullable()->after('temperature')->comment('Humedad en %');
            $table->decimal('wind_speed', 5, 2)->nullable()->after('humidity')->comment('Velocidad viento km/h');
            $table->integer('wind_direction')->nullable()->after('wind_speed')->comment('Dirección viento 0-360°');
            $table->decimal('simulation_speed', 3, 1)->default(1)->after('wind_direction')->comment('Velocidad de simulación');
            $table->integer('fire_risk')->nullable()->after('simulation_speed')->comment('Riesgo de incendio %');
            
            // Coordenadas del centro del mapa
            $table->decimal('map_center_lat', 10, 7)->nullable()->after('fire_risk')->comment('Latitud centro mapa');
            $table->decimal('map_center_lng', 10, 7)->nullable()->after('map_center_lat')->comment('Longitud centro mapa');
            
            // Metadatos de la simulación
            $table->json('initial_fires')->nullable()->after('map_center_lng')->comment('Focos iniciales JSON');
            $table->json('mitigation_strategies')->nullable()->after('initial_fires')->comment('Estrategias de mitigación');
            $table->boolean('auto_stopped')->default(false)->after('mitigation_strategies')->comment('Si se detuvo automáticamente');
        });

        // Crear tabla para almacenar el historial de propagación de cada foco en la simulación
        Schema::create('simulation_fire_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('simulacion_id');
            $table->integer('fire_id')->comment('ID interno del foco en la simulación');
            $table->integer('time_step')->comment('Paso de tiempo (hora)');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('intensity', 3, 2);
            $table->decimal('spread', 5, 3);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->foreign('simulacion_id')
                ->references('id')
                ->on('simulaciones')
                ->onDelete('cascade');
            
            $table->index(['simulacion_id', 'time_step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_fire_history');
        
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->dropColumn([
                'temperature',
                'humidity',
                'wind_speed',
                'wind_direction',
                'simulation_speed',
                'fire_risk',
                'map_center_lat',
                'map_center_lng',
                'initial_fires',
                'mitigation_strategies',
                'auto_stopped'
            ]);
        });
    }
};
