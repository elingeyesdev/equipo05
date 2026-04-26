<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds user tracking for reporting:
     * - Restores FK constraint for focos_incendios.reported_by → users
     * - Adds reported_by column to biomasas table → users
     */
    public function up(): void
    {
        // Add FK constraint to focos_incendios.reported_by (column already exists, just add constraint)
        Schema::table('focos_incendios', function (Blueprint $table) {
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
        });

        // Add reported_by column to biomasas and create FK
        Schema::table('biomasas', function (Blueprint $table) {
            $table->unsignedBigInteger('reported_by')->nullable()->after('descripcion');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FK from biomasas
        Schema::table('biomasas', function (Blueprint $table) {
            $table->dropForeign(['reported_by']);
            $table->dropColumn('reported_by');
        });

        // Drop FK from focos_incendios (keep column)
        Schema::table('focos_incendios', function (Blueprint $table) {
            $table->dropForeign(['reported_by']);
        });
    }
};
