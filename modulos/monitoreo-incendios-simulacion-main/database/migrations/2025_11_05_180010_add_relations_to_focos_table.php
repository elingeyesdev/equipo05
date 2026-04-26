<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('focos_incendios', function (Blueprint $table) {
            // Link foco to a simulation when applicable
            $table->unsignedBigInteger('simulacion_id')->nullable()->after('id');
            $table->foreign('simulacion_id')->references('id')->on('simulaciones')->onDelete('set null');

            // Link foco to a biomasa (area) if applicable
            $table->unsignedBigInteger('biomasa_id')->nullable()->after('simulacion_id');
            $table->foreign('biomasa_id')->references('id')->on('biomasas')->onDelete('set null');

            // User who reported this foco
            $table->unsignedBigInteger('reported_by')->nullable()->after('biomasa_id');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('focos_incendios', function (Blueprint $table) {
            $table->dropForeign(['simulacion_id']);
            $table->dropColumn('simulacion_id');

            $table->dropForeign(['biomasa_id']);
            $table->dropColumn('biomasa_id');

            $table->dropForeign(['reported_by']);
            $table->dropColumn('reported_by');
        });
    }
};
