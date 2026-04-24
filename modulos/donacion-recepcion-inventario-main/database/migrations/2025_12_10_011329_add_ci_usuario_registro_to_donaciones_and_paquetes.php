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
        Schema::table('donaciones', function (Blueprint $table) {
            $table->string('ci_usuario_registro', 20)->nullable()->after('fecha');
        });

        Schema::table('paquetes', function (Blueprint $table) {
            $table->string('ci_usuario_registro', 20)->nullable()->after('fecha_creacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donaciones', function (Blueprint $table) {
            $table->dropColumn('ci_usuario_registro');
        });

        Schema::table('paquetes', function (Blueprint $table) {
            $table->dropColumn('ci_usuario_registro');
        });
    }
};



