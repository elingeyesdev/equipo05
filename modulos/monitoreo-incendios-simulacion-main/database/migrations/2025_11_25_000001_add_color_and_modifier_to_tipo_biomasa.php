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
        Schema::table('tipo_biomasa', function (Blueprint $table) {
            $table->string('color', 7)->default('#4CAF50')->after('tipo_biomasa');
            $table->decimal('modificador_intensidad', 3, 2)->default(1.0)->after('color')->comment('Multiplicador de intensidad del fuego (0.5 a 2.0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_biomasa', function (Blueprint $table) {
            $table->dropColumn(['color', 'modificador_intensidad']);
        });
    }
};
