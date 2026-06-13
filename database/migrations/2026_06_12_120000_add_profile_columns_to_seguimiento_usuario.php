<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'seguimiento';

    public function up(): void
    {
        $c = $this->c;

        if (! Schema::connection($c)->hasTable('usuario')) {
            return;
        }

        Schema::connection($c)->table('usuario', function (Blueprint $table) use ($c) {
            if (! Schema::connection($c)->hasColumn('usuario', 'ci')) {
                $table->string('ci', 40)->nullable();
            }
            if (! Schema::connection($c)->hasColumn('usuario', 'tipo_sangre')) {
                $table->string('tipo_sangre', 5)->nullable();
            }
            if (! Schema::connection($c)->hasColumn('usuario', 'telefono')) {
                $table->string('telefono', 40)->nullable();
            }
        });

        $tipos = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
        $rows = DB::connection($c)->table('usuario')->orderBy('id_usuario')->get(['id_usuario', 'ci', 'tipo_sangre']);

        foreach ($rows as $i => $row) {
            $update = [];
            if (empty($row->ci)) {
                $update['ci'] = (string) (5800000 + (int) $row->id_usuario + $i);
            }
            if (empty($row->tipo_sangre)) {
                $update['tipo_sangre'] = $tipos[$i % count($tipos)];
            }
            if ($update !== []) {
                DB::connection($c)->table('usuario')->where('id_usuario', $row->id_usuario)->update($update);
            }
        }
    }

    public function down(): void
    {
        $c = $this->c;

        if (! Schema::connection($c)->hasTable('usuario')) {
            return;
        }

        Schema::connection($c)->table('usuario', function (Blueprint $table) use ($c) {
            foreach (['telefono', 'tipo_sangre', 'ci'] as $column) {
                if (Schema::connection($c)->hasColumn('usuario', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
