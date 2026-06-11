<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'seguimiento';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $c = $this->c;

        // Table evaluacion_tokens
        Schema::connection($c)->table('evaluacion_tokens', function (Blueprint $table) {
            if (!Schema::connection($this->c)->hasColumn('evaluacion_tokens', 'id_voluntario')) {
                $table->unsignedBigInteger('id_voluntario')->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('evaluacion_tokens', 'usado')) {
                $table->boolean('usado')->default(false);
            }
            if (!Schema::connection($this->c)->hasColumn('evaluacion_tokens', 'fecha_expiracion')) {
                $table->timestamp('fecha_expiracion')->nullable();
            }
        });

        // Table capacitacion
        Schema::connection($c)->table('capacitacion', function (Blueprint $table) {
            if (!Schema::connection($this->c)->hasColumn('capacitacion', 'descripcion')) {
                $table->text('descripcion')->nullable();
            }
        });

        // Table necesidad
        Schema::connection($c)->table('necesidad', function (Blueprint $table) {
            if (!Schema::connection($this->c)->hasColumn('necesidad', 'descripcion')) {
                $table->text('descripcion')->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('necesidad', 'tipo')) {
                $table->string('tipo', 100)->nullable();
            }
        });

        // Table solicitudes_ayuda
        Schema::connection($c)->table('solicitudes_ayuda', function (Blueprint $table) {
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'voluntario_id')) {
                $table->unsignedBigInteger('voluntario_id')->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'prioridad')) {
                $table->string('prioridad', 20)->default('medio');
            }
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'tipo')) {
                $table->string('tipo', 100)->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'direccion')) {
                $table->text('direccion')->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'descripcion')) {
                $table->text('descripcion')->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'latitud')) {
                $table->double('latitud')->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'longitud')) {
                $table->double('longitud')->nullable();
            }
            if (!Schema::connection($this->c)->hasColumn('solicitudes_ayuda', 'fecha')) {
                $table->timestamp('fecha')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $c = $this->c;

        Schema::connection($c)->table('solicitudes_ayuda', function (Blueprint $table) {
            $table->dropColumn(['voluntario_id', 'prioridad', 'tipo', 'direccion', 'descripcion', 'latitud', 'longitud', 'fecha']);
        });

        Schema::connection($c)->table('necesidad', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'tipo']);
        });

        Schema::connection($c)->table('capacitacion', function (Blueprint $table) {
            $table->dropColumn('descripcion');
        });

        Schema::connection($c)->table('evaluacion_tokens', function (Blueprint $table) {
            $table->dropColumn(['id_voluntario', 'usado', 'fecha_expiracion']);
        });
    }
};
