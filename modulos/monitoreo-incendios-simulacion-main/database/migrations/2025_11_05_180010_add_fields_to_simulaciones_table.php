<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->string('nombre')->after('creator_id');
            $table->dateTime('fecha')->nullable()->after('nombre');
            $table->integer('duracion')->nullable()->after('fecha');
            $table->integer('focos_activos')->default(0)->after('duracion');
            $table->integer('num_voluntarios_enviados')->default(0)->after('focos_activos');
            $table->string('estado')->default('pendiente')->after('num_voluntarios_enviados');
        });
    }

    public function down(): void
    {
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->dropColumn(['nombre','fecha','duracion','focos_activos','num_voluntarios_enviados','estado']);
        });
    }
};
