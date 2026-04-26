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
        Schema::table('simulation_fire_history', function (Blueprint $table) {
            // Cambiar fire_id de integer a string para soportar IDs largos generados en JS
            $table->string('fire_id', 100)->change();
            
            // Aumentar precisión de intensity para permitir valores mayores
            $table->decimal('intensity', 5, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulation_fire_history', function (Blueprint $table) {
            $table->integer('fire_id')->change();
            $table->decimal('intensity', 3, 2)->change();
        });
    }
};
