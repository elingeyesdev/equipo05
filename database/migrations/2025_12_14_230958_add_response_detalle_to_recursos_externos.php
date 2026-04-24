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
        Schema::table('recursos_externos', function (Blueprint $table) {
            // Aquí se guardará TODA la respuesta del endpoint con ID (sea lo que sea)
            $table->longText('response_detalle')->nullable()->after('datos_extra');
            
            // Para saber cuándo actualizamos este detalle por última vez
            $table->timestamp('detalle_cached_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('recursos_externos', function (Blueprint $table) {
            $table->dropColumn(['response_detalle', 'detalle_cached_at']);
        });
    }
};
