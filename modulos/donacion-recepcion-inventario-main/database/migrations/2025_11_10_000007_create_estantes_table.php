<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estantes', function (Blueprint $table) {
            $table->increments('id_estante');
            $table->unsignedInteger('id_almacen')->nullable();
            $table->string('codigo_estante', 50)->nullable();
            $table->text('descripcion')->nullable();

            $table->foreign('id_almacen')->references('id_almacen')->on('almacenes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estantes');
    }
};



