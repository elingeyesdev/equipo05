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
        Schema::table('biomasas', function (Blueprint $table) {
            // Change area_m2 from integer to bigInteger to support large areas
            // A bigInteger can handle values up to 9,223,372,036,854,775,807
            $table->bigInteger('area_m2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            // Revert back to integer (may cause data loss if values exceed integer limit)
            $table->integer('area_m2')->nullable()->change();
        });
    }
};
