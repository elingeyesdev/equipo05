<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Eliminar columna id_rol de usuarios (ya no la necesitamos)
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_rol']); // Si existe foreign key
            $table->dropColumn('id_rol');
        });

        // 2. Eliminar tabla roles antigua
        Schema::dropIfExists('roles');

        // 3. Renombrar spatie_roles a roles (nombre estándar de Spatie)
        Schema::rename('spatie_roles', 'roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir: renombrar roles a spatie_roles
        Schema::rename('roles', 'spatie_roles');

        // Recrear tabla roles antigua
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre_rol', 50);
            $table->text('descripcion_rol')->nullable();
        });

        // Restaurar columna id_rol en usuarios
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedInteger('id_rol')->nullable();
        });
    }
};




