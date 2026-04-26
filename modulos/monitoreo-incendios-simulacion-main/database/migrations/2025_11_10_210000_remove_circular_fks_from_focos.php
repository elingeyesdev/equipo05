<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes foreign keys that create circular dependencies:
     * - focos_incendios.reported_by (breaks users → simulaciones → focos → users cycle)
     * - focos_incendios.biomasa_id (breaks focos ↔ biomasas cycle)
     * 
     * The columns remain for application-level relationships, but without DB constraints.
     */
    public function up(): void
    {
        Schema::table('focos_incendios', function (Blueprint $table) {
            // Drop foreign key for reported_by (keeps column, removes constraint)
            $table->dropForeign(['reported_by']);
            
            // Drop foreign key for biomasa_id (keeps column, removes constraint)
            $table->dropForeign(['biomasa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('focos_incendios', function (Blueprint $table) {
            // Restore foreign keys
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('biomasa_id')->references('id')->on('biomasas')->onDelete('set null');
        });
    }
};
