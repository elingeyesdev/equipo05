<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donaciones', function (Blueprint $table) {
            $table->text('deleted_reason')->nullable()->after('deleted_by');
        });

        Schema::table('paquetes', function (Blueprint $table) {
            $table->text('deleted_reason')->nullable()->after('deleted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donaciones', function (Blueprint $table) {
            $table->dropColumn('deleted_reason');
        });

        Schema::table('paquetes', function (Blueprint $table) {
            $table->dropColumn('deleted_reason');
        });
    }
};



