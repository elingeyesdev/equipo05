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
        Schema::table('focos_incendios', function (Blueprint $table) {
            // Add index on fecha for faster queries
            if (!Schema::hasColumn('focos_incendios', 'fecha')) {
                return; // fecha column doesn't exist yet
            }
            
            $table->index('fecha');
            $table->index('intensidad');
            $table->index('created_at');
            
            // Composite index for common query patterns
            $table->index(['fecha', 'intensidad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('focos_incendios', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
            $table->dropIndex(['intensidad']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['fecha', 'intensidad']);
        });
    }
};
