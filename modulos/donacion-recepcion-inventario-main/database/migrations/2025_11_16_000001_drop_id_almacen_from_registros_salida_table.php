<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registros_salida', function (Blueprint $table) {
            if (Schema::hasColumn('registros_salida', 'id_almacen')) {
                // Drop foreign key if exists
                try {
                    $table->dropForeign(['id_almacen']);
                } catch (\Exception $e) {
                    // ignore if constraint name differs or doesn't exist
                }

                $table->dropColumn('id_almacen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registros_salida', function (Blueprint $table) {
            $table->unsignedInteger('id_almacen')->nullable()->after('id_paquete');
            $table->foreign('id_almacen')->references('id_almacen')->on('almacenes')->onDelete('set null');
        });
    }
};



