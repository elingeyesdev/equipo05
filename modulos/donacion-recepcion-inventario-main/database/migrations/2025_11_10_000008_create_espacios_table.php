<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('espacios', function (Blueprint $table) {
            $table->increments('id_espacio');
            $table->unsignedInteger('id_estante')->nullable();
            $table->string('codigo_espacio', 50)->nullable();

            // capacidad and unidad_medida removed per final schema

            $table->foreign('id_estante')->references('id_estante')->on('estantes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('espacios');
    }
};



