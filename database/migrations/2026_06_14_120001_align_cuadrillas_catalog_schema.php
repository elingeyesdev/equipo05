<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'cuadrillas';

    public function up(): void
    {
        $c = $this->c;

        if (Schema::connection($c)->hasTable('estado_sistema')) {
            Schema::connection($c)->table('estado_sistema', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('estado_sistema', 'codigo')) {
                    $table->string('codigo', 120)->nullable();
                }
                if (! Schema::connection($c)->hasColumn('estado_sistema', 'color')) {
                    $table->string('color', 20)->nullable();
                }
                if (! Schema::connection($c)->hasColumn('estado_sistema', 'tabla')) {
                    $table->string('tabla', 120)->nullable();
                }
            });
        }

        if (Schema::connection($c)->hasTable('noticia')) {
            Schema::connection($c)->table('noticia', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('noticia', 'descripcion')) {
                    $table->text('descripcion')->nullable();
                }
                if (! Schema::connection($c)->hasColumn('noticia', 'url')) {
                    $table->string('url', 500)->nullable();
                }
                if (! Schema::connection($c)->hasColumn('noticia', 'image')) {
                    $table->string('image', 500)->nullable();
                }
                if (! Schema::connection($c)->hasColumn('noticia', 'date')) {
                    $table->timestamp('date')->nullable();
                }
            });
        }

        if (Schema::connection($c)->hasTable('curso')) {
            Schema::connection($c)->table('curso', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('curso', 'descripcion')) {
                    $table->text('descripcion')->nullable();
                }
            });
        }

        if (Schema::connection($c)->hasTable('kardex')) {
            Schema::connection($c)->table('kardex', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('kardex', 'descripcion')) {
                    $table->string('descripcion', 255)->nullable();
                }
            });
        }

        if (Schema::connection($c)->hasTable('consultas')) {
            Schema::connection($c)->table('consultas', function (Blueprint $table) use ($c) {
                if (! Schema::connection($c)->hasColumn('consultas', 'asunto')) {
                    $table->string('asunto', 200)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $c = $this->c;

        if (Schema::connection($c)->hasTable('estado_sistema')) {
            Schema::connection($c)->table('estado_sistema', function (Blueprint $table) use ($c) {
                foreach (['tabla', 'color', 'codigo'] as $column) {
                    if (Schema::connection($c)->hasColumn('estado_sistema', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::connection($c)->hasTable('noticia')) {
            Schema::connection($c)->table('noticia', function (Blueprint $table) use ($c) {
                foreach (['date', 'image', 'url', 'descripcion'] as $column) {
                    if (Schema::connection($c)->hasColumn('noticia', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::connection($c)->hasTable('curso') && Schema::connection($c)->hasColumn('curso', 'descripcion')) {
            Schema::connection($c)->table('curso', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }

        if (Schema::connection($c)->hasTable('kardex') && Schema::connection($c)->hasColumn('kardex', 'descripcion')) {
            Schema::connection($c)->table('kardex', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }

        if (Schema::connection($c)->hasTable('consultas') && Schema::connection($c)->hasColumn('consultas', 'asunto')) {
            Schema::connection($c)->table('consultas', function (Blueprint $table) {
                $table->dropColumn('asunto');
            });
        }
    }
};
