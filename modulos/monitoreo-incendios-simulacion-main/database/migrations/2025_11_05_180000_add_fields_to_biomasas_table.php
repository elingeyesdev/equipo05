<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            $table->string('nombre')->after('id');
            $table->string('tipo')->nullable()->after('nombre');
            $table->integer('area_m2')->nullable()->after('tipo');
            $table->float('densidad')->nullable()->after('area_m2');
            $table->float('humedad')->nullable()->after('densidad');
            $table->string('ubicacion')->nullable()->after('humedad');
            $table->text('descripcion')->nullable()->after('ubicacion');
        });
    }

    public function down(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            $table->dropColumn(['nombre','tipo','area_m2','densidad','humedad','ubicacion','descripcion']);
        });
    }
};
