<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! filter_var(env('DATABASE_UNIFIED_POSTGRES', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        if (! Schema::connection('core')->hasTable('usuarios')) {
            return;
        }

        if (! Schema::connection('core')->hasColumn('usuarios', 'remember_token')) {
            Schema::connection('core')->table('usuarios', function (Blueprint $table) {
                $table->string('remember_token', 100)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (! filter_var(env('DATABASE_UNIFIED_POSTGRES', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        if (Schema::connection('core')->hasColumn('usuarios', 'remember_token')) {
            Schema::connection('core')->table('usuarios', function (Blueprint $table) {
                $table->dropColumn('remember_token');
            });
        }
    }
};
