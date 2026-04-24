<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombres', 100);
            $table->string('apellidos', 150);
            $table->string('ci', 20)->unique();
            $table->text('foto_ci')->nullable();
            $table->string('licencia_conducir', 50)->nullable();
            $table->text('foto_licencia')->nullable();
            $table->string('genero', 20)->nullable();
            $table->string('correo', 100)->unique();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion_domicilio')->nullable();
            $table->text('contrasena');
            $table->string('estado', 20)->default('Activo');
            $table->string('entidad_pertenencia', 150)->nullable();
            $table->string('tipo_sangre', 5)->nullable();
            $table->unsignedInteger('id_rol')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('id_rol')->references('id_rol')->on('roles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};



