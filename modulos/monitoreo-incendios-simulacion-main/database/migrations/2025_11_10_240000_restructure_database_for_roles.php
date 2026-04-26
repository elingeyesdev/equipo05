<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration restructures the database for role-based access:
     * 1. Rename clientes → voluntarios
     * 2. Change simulaciones.creator_id → admin_id (FK to administradores)
     * 3. Change biomasas.reported_by → user_id (anyone can create)
     * 4. Remove focos_incendios connections (API source, no user/biomasa links)
     * 5. Separate predictions from simulaciones, connect to focos_incendios
     */
    public function up(): void
    {
        // 1. Rename clientes → voluntarios
        Schema::rename('clientes', 'voluntarios');

        // 2. Restructure simulaciones: creator_id (users) → admin_id (administradores)
        Schema::table('simulaciones', function (Blueprint $table) {
            // Drop old FK
            $table->dropForeign(['creator_id']);
            
            // Remove creator_id and add admin_id
            $table->dropColumn('creator_id');
            $table->unsignedBigInteger('admin_id')->nullable()->after('id');
            
            // Create FK to administradores
            $table->foreign('admin_id')->references('id')->on('administradores')->onDelete('set null');
        });

        // 3. Restructure biomasas: reported_by → user_id (anyone can create)
        Schema::table('biomasas', function (Blueprint $table) {
            // Drop old FK
            $table->dropForeign(['reported_by']);
            
            // Rename column
            $table->renameColumn('reported_by', 'user_id');
            
            // Create new FK
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 4. Clean focos_incendios (API source, no user/biomasa connections)
        Schema::table('focos_incendios', function (Blueprint $table) {
            // Drop FKs only if they exist
            if (Schema::hasColumn('focos_incendios', 'reported_by')) {
                $table->dropForeign(['reported_by']);
            }
            
            // Drop columns if they exist
            if (Schema::hasColumn('focos_incendios', 'reported_by')) {
                $table->dropColumn('reported_by');
            }
            if (Schema::hasColumn('focos_incendios', 'biomasa_id')) {
                $table->dropColumn('biomasa_id');
            }
        });

        // 5. Separate predictions from simulaciones, connect to focos_incendios
        Schema::table('predictions', function (Blueprint $table) {
            // Drop simulacion FK
            $table->dropForeign(['simulacion_id']);
            $table->dropColumn('simulacion_id');
            
            // Add foco_incendio_id
            $table->unsignedBigInteger('foco_incendio_id')->nullable()->after('id');
            $table->foreign('foco_incendio_id')->references('id')->on('focos_incendios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse predictions
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropForeign(['foco_incendio_id']);
            $table->dropColumn('foco_incendio_id');
            
            $table->unsignedBigInteger('simulacion_id')->nullable()->after('id');
            $table->foreign('simulacion_id')->references('id')->on('simulaciones')->onDelete('cascade');
        });

        // Restore focos_incendios columns
        Schema::table('focos_incendios', function (Blueprint $table) {
            $table->unsignedBigInteger('biomasa_id')->nullable();
            $table->unsignedBigInteger('reported_by')->nullable();
            
            $table->foreign('biomasa_id')->references('id')->on('biomasas')->onDelete('set null');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
        });

        // Restore biomasas.user_id → reported_by
        Schema::table('biomasas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'reported_by');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
        });

        // Restore simulaciones.admin_id → creator_id
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
            
            $table->unsignedBigInteger('creator_id')->nullable()->after('id');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
        });

        // Rename voluntarios → clientes
        Schema::rename('voluntarios', 'clientes');
    }
};
