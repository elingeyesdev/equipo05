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
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->boolean('public')->default(false)->after('auto_stopped');
            $table->index('public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->dropIndex(['public']);
            $table->dropColumn('public');
        });
    }
};
