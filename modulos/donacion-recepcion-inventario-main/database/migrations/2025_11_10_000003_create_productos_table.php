<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id_producto');
            $table->unsignedInteger('id_categoria')->nullable();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida', 50)->nullable();

            $table->foreign('id_categoria')->references('id_categoria')->on('categorias_productos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};



