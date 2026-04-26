<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            if (!Schema::hasColumn('biomasas', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('simulaciones', function (Blueprint $table) {
            if (!Schema::hasColumn('simulaciones', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('predictions', function (Blueprint $table) {
            if (!Schema::hasColumn('predictions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('biomasas', function (Blueprint $table) {
            if (Schema::hasColumn('biomasas', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('simulaciones', function (Blueprint $table) {
            if (Schema::hasColumn('simulaciones', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('predictions', function (Blueprint $table) {
            if (Schema::hasColumn('predictions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
