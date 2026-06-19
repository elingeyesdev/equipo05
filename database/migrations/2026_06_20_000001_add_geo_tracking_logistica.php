<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'logistica';

    public function up(): void
    {
        if (Schema::connection($this->c)->hasTable('ubicacion')) {
            Schema::connection($this->c)->table('ubicacion', function (Blueprint $table) {
                if (! Schema::connection($this->c)->hasColumn('ubicacion', 'latitud')) {
                    $table->decimal('latitud', 10, 7)->nullable()->after('descripcion');
                }
                if (! Schema::connection($this->c)->hasColumn('ubicacion', 'longitud')) {
                    $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
                }
                if (! Schema::connection($this->c)->hasColumn('ubicacion', 'zona')) {
                    $table->string('zona', 255)->nullable()->after('longitud');
                }
            });
        }

        if (Schema::connection($this->c)->hasTable('historial_seguimiento_donaciones')) {
            Schema::connection($this->c)->table('historial_seguimiento_donaciones', function (Blueprint $table) {
                if (! Schema::connection($this->c)->hasColumn('historial_seguimiento_donaciones', 'id_ubicacion')) {
                    $table->unsignedBigInteger('id_ubicacion')->nullable()->after('id_paquete');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection($this->c)->hasTable('historial_seguimiento_donaciones')
            && Schema::connection($this->c)->hasColumn('historial_seguimiento_donaciones', 'id_ubicacion')) {
            Schema::connection($this->c)->table('historial_seguimiento_donaciones', function (Blueprint $table) {
                $table->dropColumn('id_ubicacion');
            });
        }

        if (Schema::connection($this->c)->hasTable('ubicacion')) {
            Schema::connection($this->c)->table('ubicacion', function (Blueprint $table) {
                foreach (['zona', 'longitud', 'latitud'] as $col) {
                    if (Schema::connection($this->c)->hasColumn('ubicacion', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
