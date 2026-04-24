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
        Schema::table('registros_salida', function (Blueprint $table) {
            $table->string('encargado', 255)->nullable()->after('destino');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_salida', function (Blueprint $table) {
            $table->dropColumn('encargado');
        });
    }
};



