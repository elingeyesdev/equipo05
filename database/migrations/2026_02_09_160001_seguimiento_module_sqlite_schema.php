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

        Schema::connection($c)->create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->string('nombre', 150)->nullable();
            $table->string('apellido', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('administrador')->default(false);
            $table->timestamps();
        });

        Schema::connection($c)->create('evaluacion', function (Blueprint $table) {
            $table->bigIncrements('id_evaluacion');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('titulo', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('evaluacion_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 128)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('capacitacion', function (Blueprint $table) {
            $table->bigIncrements('id_capacitacion');
            $table->string('nombre', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('necesidad', function (Blueprint $table) {
            $table->bigIncrements('id_necesidad');
            $table->string('nombre', 200)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('solicitudes_ayuda', function (Blueprint $table) {
            $table->id();
            $table->string('estado', 80)->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('chat_mensajes', function (Blueprint $table) {
            $table->id();
            $table->text('mensaje')->nullable();
            $table->timestamps();
        });

        Schema::connection($c)->create('universidad', function (Blueprint $table) {
            $table->bigIncrements('id_universidad');
            $table->string('nombre', 200)->nullable();
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
        foreach (['consultas', 'universidad', 'chat_mensajes', 'solicitudes_ayuda', 'necesidad', 'capacitacion', 'evaluacion_tokens', 'evaluacion', 'usuario'] as $t) {
            Schema::connection($c)->dropIfExists($t);
        }
    }
};
