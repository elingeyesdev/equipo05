<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $c = 'logistica';

    public function up(): void
    {
        $c = $this->c;

        if (Schema::connection($c)->hasTable('estado')) {
            return;
        }

        Schema::connection($c)->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::connection($c)->create('estado', function (Blueprint $table) {
            $table->bigIncrements('id_estado');
            $table->string('nombre_estado', 120);
            $table->timestamps();
        });

        Schema::connection($c)->create('solicitante', function (Blueprint $table) {
            $table->bigIncrements('id_solicitante');
            $table->string('nombre', 120);
            $table->string('apellido', 120)->nullable();
            $table->string('ci', 40);
            $table->string('telefono', 40)->nullable();
            $table->string('email', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('destino', function (Blueprint $table) {
            $table->bigIncrements('id_destino');
            $table->string('comunidad', 120);
            $table->string('provincia', 120);
            $table->string('direccion', 255)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('solicitud', function (Blueprint $table) {
            $table->bigIncrements('id_solicitud');
            $table->string('estado', 40)->default('pendiente');
            $table->string('codigo_seguimiento', 64);
            $table->unsignedInteger('cantidad_personas')->default(1);
            $table->date('fecha_inicio')->nullable();
            $table->string('tipo_emergencia', 120)->nullable();
            $table->text('insumos_necesarios')->nullable();
            $table->unsignedBigInteger('id_solicitante');
            $table->unsignedBigInteger('id_destino');
            $table->date('fecha_solicitud')->nullable();
            $table->boolean('aprobada')->default(false);
            $table->boolean('apoyoaceptado')->default(false);
            $table->date('fecha_necesidad')->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('paquete', function (Blueprint $table) {
            $table->bigIncrements('id_paquete');
            $table->unsignedBigInteger('id_solicitud')->nullable();
            $table->string('codigo', 64)->nullable();
            $table->string('ubicacion_actual', 255)->nullable();
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('historial_seguimiento_donaciones', function (Blueprint $table) {
            $table->bigIncrements('id_historial');
            $table->unsignedBigInteger('id_paquete')->nullable();
            $table->string('estado', 80)->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->string('vehiculo_placa', 32)->nullable();
            $table->string('conductor_nombre', 120)->nullable();
            $table->string('conductor_ci', 40)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('ubicacion', function (Blueprint $table) {
            $table->bigIncrements('id_ubicacion');
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('vehiculo', function (Blueprint $table) {
            $table->bigIncrements('id_vehiculo');
            $table->string('placa', 32)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('conductor', function (Blueprint $table) {
            $table->bigIncrements('id_conductor');
            $table->string('nombre', 120)->nullable();
            $table->string('apellido', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('tipo_vehiculo', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_vehiculo');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('tipo_licencia', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_licencia');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('tipo_emergencia', function (Blueprint $table) {
            $table->bigIncrements('id_tipo_emergencia');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('marca', function (Blueprint $table) {
            $table->bigIncrements('id_marca');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('reporte', function (Blueprint $table) {
            $table->bigIncrements('id_reporte');
            $table->string('titulo', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('rol', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 120)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $c = $this->c;
        foreach ([
            'rol', 'usuario', 'reporte', 'marca', 'tipo_emergencia', 'tipo_licencia', 'tipo_vehiculo',
            'conductor', 'vehiculo', 'ubicacion', 'historial_seguimiento_donaciones', 'paquete', 'solicitud',
            'destino', 'solicitante', 'estado', 'users',
        ] as $t) {
            Schema::connection($c)->dropIfExists($t);
        }
    }
};
