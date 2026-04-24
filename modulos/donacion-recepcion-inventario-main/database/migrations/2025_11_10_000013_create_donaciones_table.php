<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donaciones', function (Blueprint $table) {
            $table->increments('id_donacion');
            $table->unsignedInteger('id_donante')->nullable();
            $table->enum('tipo', ['dinero','especie','ropa'])->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->unsignedInteger('id_campana')->nullable();
            $table->unsignedInteger('id_punto_recoleccion')->nullable();
            $table->text('observaciones')->nullable();

            $table->foreign('id_donante')->references('id_donante')->on('donantes')->onDelete('set null');
            $table->foreign('id_campana')->references('id_campana')->on('campanas')->onDelete('set null');
            $table->foreign('id_punto_recoleccion')->references('id_punto')->on('puntos_recoleccion')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donaciones');
    }
};



