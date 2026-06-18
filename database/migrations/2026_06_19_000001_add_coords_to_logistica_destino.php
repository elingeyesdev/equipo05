<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'logistica';

    public function up(): void
    {
        if (! Schema::connection($this->c)->hasTable('destino')) {
            return;
        }

        Schema::connection($this->c)->table('destino', function (Blueprint $table) {
            if (! Schema::connection($this->c)->hasColumn('destino', 'latitud')) {
                $table->decimal('latitud', 10, 7)->nullable()->after('direccion');
            }
            if (! Schema::connection($this->c)->hasColumn('destino', 'longitud')) {
                $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::connection($this->c)->hasTable('destino')) {
            return;
        }

        Schema::connection($this->c)->table('destino', function (Blueprint $table) {
            if (Schema::connection($this->c)->hasColumn('destino', 'latitud')) {
                $table->dropColumn('latitud');
            }
            if (Schema::connection($this->c)->hasColumn('destino', 'longitud')) {
                $table->dropColumn('longitud');
            }
        });
    }
};
