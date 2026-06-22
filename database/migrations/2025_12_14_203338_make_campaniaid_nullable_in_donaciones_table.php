<?php

use App\Support\UnifiedPostgres;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (UnifiedPostgres::enabled() || ! Schema::hasTable('donaciones')) {
            return;
        }

        Schema::table('donaciones', function (Blueprint $table) {
            $table->integer('campaniaid')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('donaciones', function (Blueprint $table) {
            $table->integer('campaniaid')->nullable(false)->change();
        });
    }
};
