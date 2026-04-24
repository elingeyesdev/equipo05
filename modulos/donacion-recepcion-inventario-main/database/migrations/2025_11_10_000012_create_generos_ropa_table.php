<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generos_ropa', function (Blueprint $table) {
            $table->increments('id_genero');
            $table->string('genero', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generos_ropa');
    }
};



