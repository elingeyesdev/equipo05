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
        // Agregar ci_usuario a biomasas
        Schema::table('biomasas', function (Blueprint $table) {
            $table->string('ci_usuario', 20)->nullable()->after('user_id');
            $table->index('ci_usuario');
        });

        // Agregar ci_usuario a simulaciones
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->string('ci_usuario', 20)->nullable()->after('admin_id');
            $table->index('ci_usuario');
        });

        // Agregar user_id y ci_usuario a predictions
        Schema::table('predictions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('meta');
            $table->string('ci_usuario', 20)->nullable()->after('user_id');
            $table->index('ci_usuario');
        });

        // Poblar datos existentes con el CI del usuario
        if (\DB::getDriverName() === 'sqlite') {
            \DB::statement('
                UPDATE biomasas
                SET ci_usuario = (
                    SELECT users.cedula_identidad
                    FROM users
                    WHERE users.id = biomasas.user_id
                      AND users.cedula_identidad IS NOT NULL
                    LIMIT 1
                )
                WHERE user_id IS NOT NULL
            ');

            \DB::statement('
                UPDATE simulaciones
                SET ci_usuario = (
                    SELECT users.cedula_identidad
                    FROM users
                    WHERE users.id = simulaciones.admin_id
                      AND users.cedula_identidad IS NOT NULL
                    LIMIT 1
                )
                WHERE admin_id IS NOT NULL
            ');
        } else {
            \DB::statement('
                UPDATE biomasas b
                SET ci_usuario = u.cedula_identidad
                FROM users u
                WHERE b.user_id = u.id
                  AND b.user_id IS NOT NULL
                  AND u.cedula_identidad IS NOT NULL
            ');

            \DB::statement('
                UPDATE simulaciones s
                SET ci_usuario = u.cedula_identidad
                FROM users u
                WHERE s.admin_id = u.id
                  AND s.admin_id IS NOT NULL
                  AND u.cedula_identidad IS NOT NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            $table->dropIndex(['ci_usuario']);
            $table->dropColumn('ci_usuario');
        });

        Schema::table('simulaciones', function (Blueprint $table) {
            $table->dropIndex(['ci_usuario']);
            $table->dropColumn('ci_usuario');
        });

        Schema::table('predictions', function (Blueprint $table) {
            $table->dropIndex(['ci_usuario']);
            $table->dropColumn(['user_id', 'ci_usuario']);
        });
    }
};
