<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates tipo_biomasa catalog table and connects it to biomasas
     */
    public function up(): void
    {
        // 1. Create tipo_biomasa table
        Schema::create('tipo_biomasa', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_biomasa')->unique();
            $table->timestamps();
        });

        // 2. Modify biomasas table: tipo (string) → tipo_biomasa_id (FK)
        Schema::table('biomasas', function (Blueprint $table) {
            // Remove old tipo column
            $table->dropColumn('tipo');
            
            // Add FK to tipo_biomasa
            $table->unsignedBigInteger('tipo_biomasa_id')->nullable()->after('nombre');
            $table->foreign('tipo_biomasa_id')->references('id')->on('tipo_biomasa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore biomasas.tipo
        Schema::table('biomasas', function (Blueprint $table) {
            $table->dropForeign(['tipo_biomasa_id']);
            $table->dropColumn('tipo_biomasa_id');
            
            $table->string('tipo')->nullable()->after('nombre');
        });

        // Drop tipo_biomasa table
        Schema::dropIfExists('tipo_biomasa');
    }
};
