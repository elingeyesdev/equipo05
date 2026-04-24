<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donacion_detalles', function (Blueprint $table) {
            $table->increments('id_detalle');
            $table->unsignedInteger('id_donacion')->nullable();
            $table->unsignedInteger('id_producto')->nullable();
            $table->integer('cantidad')->default(0);
            $table->integer('cantidad_por_unidad')->nullable();
            $table->string('unidad_medida', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('codigo_unico', 50)->unique()->nullable();
            $table->unsignedInteger('id_talla')->nullable();
            $table->unsignedInteger('id_genero')->nullable();

            $table->foreign('id_donacion')->references('id_donacion')->on('donaciones')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('set null');
            $table->foreign('id_talla')->references('id_talla')->on('tallas')->onDelete('set null');
            $table->foreign('id_genero')->references('id_genero')->on('generos_ropa')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donacion_detalles');
    }
};



