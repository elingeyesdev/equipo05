<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donacion_detalles', function (Blueprint $table) {
            $table->date('fecha_caducidad')->nullable()->after('id_genero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donacion_detalles', function (Blueprint $table) {
            $table->dropColumn('fecha_caducidad');
        });
    }
};



