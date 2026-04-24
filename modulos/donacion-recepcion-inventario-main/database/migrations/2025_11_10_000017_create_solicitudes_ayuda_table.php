<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_ayuda', function (Blueprint $table) {
            $table->increments('id_solicitud');
            $table->string('codigo_externo', 100)->unique()->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->string('estado', 20)->default('pendiente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_ayuda');
    }
};



