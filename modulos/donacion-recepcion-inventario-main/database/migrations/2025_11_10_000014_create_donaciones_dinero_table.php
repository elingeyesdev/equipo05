<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donaciones_dinero', function (Blueprint $table) {
            $table->increments('id_donacion_dinero');
            $table->unsignedInteger('id_donacion')->unique();
            $table->decimal('monto', 12, 2);
            $table->string('moneda', 10)->default('BOB');
            $table->string('metodo_pago', 30)->nullable();
            $table->string('referencia_pago', 100)->nullable();
            $table->string('entidad_bancaria', 100)->nullable();
            $table->text('comprobante_imagen')->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->text('observaciones')->nullable();

            $table->foreign('id_donacion')->references('id_donacion')->on('donaciones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donaciones_dinero');
    }
};



