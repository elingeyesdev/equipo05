<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_productos', function (Blueprint $table) {
            $table->increments('id_categoria');
            $table->string('nombre', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_productos');
    }
};



