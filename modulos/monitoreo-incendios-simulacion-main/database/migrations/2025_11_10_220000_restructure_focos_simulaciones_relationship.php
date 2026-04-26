<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration restructures the relationship between focos and simulaciones:
     * - Removes direct FK from focos_incendios to simulaciones (focos exist independently)
     * - Drops simulacion_user table (volunteers not needed)
     * - Creates foco_simulacion pivot table for many-to-many relationship
     */
    public function up(): void
    {
        // Drop foreign key and column from focos_incendios
        Schema::table('focos_incendios', function (Blueprint $table) {
            $table->dropForeign(['simulacion_id']);
            $table->dropColumn('simulacion_id');
        });

        // Drop simulacion_user pivot table (volunteers not needed)
        Schema::dropIfExists('simulacion_user');

        // Create new pivot table for focos and simulaciones (many-to-many)
        Schema::create('foco_simulacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('foco_incendio_id');
            $table->unsignedBigInteger('simulacion_id');
            $table->timestamp('agregado_at')->nullable(); // when foco was added to simulation
            $table->boolean('activo')->default(true); // if foco is still active in this simulation
            $table->timestamps();

            $table->foreign('foco_incendio_id')->references('id')->on('focos_incendios')->onDelete('cascade');
            $table->foreign('simulacion_id')->references('id')->on('simulaciones')->onDelete('cascade');
            $table->unique(['foco_incendio_id', 'simulacion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new pivot table
        Schema::dropIfExists('foco_simulacion');

        // Restore simulacion_user table
        Schema::create('simulacion_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('simulacion_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->foreign('simulacion_id')->references('id')->on('simulaciones')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['simulacion_id','user_id']);
        });

        // Restore simulacion_id to focos_incendios
        Schema::table('focos_incendios', function (Blueprint $table) {
            $table->unsignedBigInteger('simulacion_id')->nullable()->after('id');
            $table->foreign('simulacion_id')->references('id')->on('simulaciones')->onDelete('set null');
        });
    }
};
