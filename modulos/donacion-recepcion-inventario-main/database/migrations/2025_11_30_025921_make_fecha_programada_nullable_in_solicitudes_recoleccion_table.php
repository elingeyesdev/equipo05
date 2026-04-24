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
        Schema::table('solicitudes_recoleccion', function (Blueprint $table) {
            $table->timestamp('fecha_programada')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_recoleccion', function (Blueprint $table) {
            $table->timestamp('fecha_programada')->nullable(false)->change();
        });
    }
};



