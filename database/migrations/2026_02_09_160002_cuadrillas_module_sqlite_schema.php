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

        if (Schema::connection($c)->hasTable('curso')) {
            return;
        }

        Schema::connection($c)->create('reporte', function (Blueprint $table) {
            $table->bigIncrements('id_reporte');
            $table->string('titulo', 255)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('reporte_incendio', function (Blueprint $table) {
            $table->bigIncrements('id_reporte_incendio');
            $table->string('titulo', 255)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('foco_calor', function (Blueprint $table) {
            $table->bigIncrements('id_foco_calor');
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('equipo', function (Blueprint $table) {
            $table->bigIncrements('id_equipo');
            $table->string('nombre', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('recurso', function (Blueprint $table) {
            $table->bigIncrements('id_recurso');
            $table->string('nombre', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('noticia', function (Blueprint $table) {
            $table->bigIncrements('id_noticia');
            $table->string('titulo', 500)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('curso', function (Blueprint $table) {
            $table->bigIncrements('id_curso');
            $table->string('nombre', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('inscrito', function (Blueprint $table) {
            $table->bigIncrements('id_inscrito');
            $table->unsignedBigInteger('id_curso')->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('comunario', function (Blueprint $table) {
            $table->bigIncrements('id_comunario');
            $table->string('nombre', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->string('nombre', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('role', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('genero', function (Blueprint $table) {
            $table->bigIncrements('id_genero');
            $table->string('nombre', 80)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('tipo_sangre', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_sangre');
            $table->string('nombre', 80)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('nivel_entrenamiento', function (Blueprint $table) {
            $table->bigIncrements('id_nivel_entrenamiento');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('nivel_gravedad', function (Blueprint $table) {
            $table->bigIncrements('id_nivel_gravedad');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('tipo_incidente', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_incidente');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('tipo_recurso', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_recurso');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('condicion_climatica', function (Blueprint $table) {
            $table->bigIncrements('id_condicion_climatica');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('estado_sistema', function (Blueprint $table) {
            $table->bigIncrements('id_estado_sistema');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('kardex', function (Blueprint $table) {
            $table->bigIncrements('id_kardex');
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('asunto', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $c = $this->c;
        foreach ([
            'consultas', 'kardex', 'estado_sistema', 'condicion_climatica', 'tipo_recurso', 'tipo_incidente',
            'nivel_gravedad', 'nivel_entrenamiento', 'tipo_sangre', 'genero', 'role', 'usuario', 'comunario',
            'inscrito', 'curso', 'noticia', 'recurso', 'equipo', 'foco_calor', 'reporte_incendio', 'reporte',
        ] as $t) {
            Schema::connection($c)->dropIfExists($t);
        }
    }
};
