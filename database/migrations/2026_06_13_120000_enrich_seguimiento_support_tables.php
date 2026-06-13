<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'seguimiento';

    public function up(): void
    {
        $c = $this->c;

        if (Schema::connection($c)->hasTable('usuario') && ! Schema::connection($c)->hasColumn('usuario', 'id_universidad')) {
            Schema::connection($c)->table('usuario', function (Blueprint $table) {
                $table->unsignedBigInteger('id_universidad')->nullable()->after('telefono');
            });
        }

        if (Schema::connection($c)->hasTable('universidad')) {
            Schema::connection($c)->table('universidad', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('universidad', 'sigla')) {
                    $table->string('sigla', 20)->nullable();
                }
                if (! Schema::connection($c)->hasColumn('universidad', 'ciudad')) {
                    $table->string('ciudad', 100)->nullable();
                }
            });
        }

        if (Schema::connection($c)->hasTable('consultas')) {
            Schema::connection($c)->table('consultas', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('consultas', 'descripcion')) {
                    $table->text('descripcion')->nullable();
                }
                if (! Schema::connection($c)->hasColumn('consultas', 'estado')) {
                    $table->string('estado', 40)->nullable()->default('abierta');
                }
                if (! Schema::connection($c)->hasColumn('consultas', 'prioridad')) {
                    $table->string('prioridad', 20)->nullable()->default('media');
                }
                if (! Schema::connection($c)->hasColumn('consultas', 'id_usuario')) {
                    $table->unsignedBigInteger('id_usuario')->nullable();
                }
            });
        }

        if (Schema::connection($c)->hasTable('chat_mensajes')) {
            Schema::connection($c)->table('chat_mensajes', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('chat_mensajes', 'id_usuario')) {
                    $table->unsignedBigInteger('id_usuario')->nullable();
                }
                if (! Schema::connection($c)->hasColumn('chat_mensajes', 'conversacion_id')) {
                    $table->unsignedBigInteger('conversacion_id')->nullable();
                }
                if (! Schema::connection($c)->hasColumn('chat_mensajes', 'remitente_tipo')) {
                    $table->string('remitente_tipo', 20)->nullable()->default('voluntario');
                }
            });
        }
    }

    public function down(): void
    {
        $c = $this->c;

        if (Schema::connection($c)->hasTable('chat_mensajes')) {
            Schema::connection($c)->table('chat_mensajes', function (Blueprint $table) use ($c) {
                foreach (['remitente_tipo', 'conversacion_id', 'id_usuario'] as $column) {
                    if (Schema::connection($c)->hasColumn('chat_mensajes', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::connection($c)->hasTable('consultas')) {
            Schema::connection($c)->table('consultas', function (Blueprint $table) use ($c) {
                foreach (['id_usuario', 'prioridad', 'estado', 'descripcion'] as $column) {
                    if (Schema::connection($c)->hasColumn('consultas', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::connection($c)->hasTable('universidad')) {
            Schema::connection($c)->table('universidad', function (Blueprint $table) use ($c) {
                foreach (['ciudad', 'sigla'] as $column) {
                    if (Schema::connection($c)->hasColumn('universidad', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::connection($c)->hasTable('usuario') && Schema::connection($c)->hasColumn('usuario', 'id_universidad')) {
            Schema::connection($c)->table('usuario', function (Blueprint $table) {
                $table->dropColumn('id_universidad');
            });
        }
    }
};
