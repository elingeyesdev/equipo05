<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('usuarioid'); // SERIAL PRIMARY KEY
            $table->string('email', 100)->unique();
            $table->string('contrasena', 255);
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('telefono', 20)->nullable();
            $table->string('imagenurl', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->dateTime('fecharegistro')->useCurrent();
            $table->timestamps(); // Laravel necesita esto para el login a veces
        });

        $tableNames = ['roles' => 'roles', 'permissions' => 'permissions', 'model_has_roles' => 'model_has_roles', 'model_has_permissions' => 'model_has_permissions', 'role_has_permissions' => 'role_has_permissions'];

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id'); // Spatie usa 'id', no 'rolid'
                $table->string('name');      // Spatie usa 'name', no 'nombre'
                $table->string('guard_name');
                $table->string('descripcion')->nullable(); // Tu campo extra del SQL
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }
        
        // Creamos las tablas intermedias de Spatie
        if (!Schema::hasTable('permissions')) {
             Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }
        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id'); // Esto se unirá con usuarioid
                $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
            });
        }
        // ... (omitimos permissions extras por brevedad, model_has_roles es lo vital)

        // ==================================================================
        // 3. TABLA: ESTADOS (Del SQL Script)
        // ==================================================================
        Schema::create('estados', function (Blueprint $table) {
            $table->id('estadoid');
            $table->string('nombre', 50);
            $table->string('descripcion', 255)->nullable();
        });

        // ==================================================================
        // 4. TABLA: CAMPANIAS (Del SQL Script + idexterno)
        // ==================================================================
        Schema::create('campanias', function (Blueprint $table) {
            $table->id('campaniaid');
            // ID EXTERNO (De tu migración extra)
            $table->integer('idexterno')->nullable()->unique(); 
            
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->date('fechainicio');
            $table->date('fechafin')->nullable();
            $table->decimal('metarecaudacion', 12, 2);
            $table->decimal('montorecaudado', 12, 2)->default(0);
            $table->unsignedBigInteger('usuarioidcreador');
            $table->boolean('activa')->default(true);
            $table->string('imagenurl', 255)->nullable();
            $table->dateTime('fechacreacion')->useCurrent();
            
            $table->foreign('usuarioidcreador')->references('usuarioid')->on('usuarios');
        });

        // ==================================================================
        // 5. TABLA: DONACIONES (Del SQL Script + idexterno)
        // ==================================================================
        Schema::create('donaciones', function (Blueprint $table) {
            $table->id('donacionid');
            // ID EXTERNO (De tu migración extra)
            $table->integer('idexterno')->nullable()->unique();

            $table->unsignedBigInteger('usuarioid')->nullable();
            $table->unsignedBigInteger('campaniaid');
            $table->decimal('monto', 12, 2)->default(0);
            $table->string('tipodonacion', 20);
            $table->text('descripcion')->nullable();
            $table->dateTime('fechadonacion')->useCurrent();
            $table->unsignedBigInteger('estadoid')->default(1);
            $table->boolean('esanonima')->default(false);
            $table->timestamps();

            $table->foreign('usuarioid')->references('usuarioid')->on('usuarios');
            $table->foreign('campaniaid')->references('campaniaid')->on('campanias');
            $table->foreign('estadoid')->references('estadoid')->on('estados');
        });

        // ==================================================================
        // 6. TABLA: ASIGNACIONES (Del SQL Script)
        // ==================================================================
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id('asignacionid');
            $table->unsignedBigInteger('campaniaid');
            $table->string('descripcion', 255);
            $table->decimal('monto', 12, 2);
            $table->dateTime('fechaasignacion')->useCurrent();
            $table->string('imagenurl', 255)->nullable();
            $table->unsignedBigInteger('usuarioid');
            $table->string('comprobante', 255)->nullable();

            $table->foreign('campaniaid')->references('campaniaid')->on('campanias');
            $table->foreign('usuarioid')->references('usuarioid')->on('usuarios');
        });

        // ==================================================================
        // 7. TABLA: DETALLESASIGNACION (Del SQL Script)
        // ==================================================================
        Schema::create('detallesasignacion', function (Blueprint $table) {
            $table->id('detalleid');
            $table->unsignedBigInteger('asignacionid');
            $table->string('concepto', 100);
            $table->integer('cantidad');
            $table->decimal('preciounitario', 10, 2);
            $table->string('imagenurl', 255)->nullable();

            $table->foreign('asignacionid')->references('asignacionid')->on('asignaciones')->onDelete('cascade');
        });

        // ==================================================================
        // 8. TABLA: MENSAJES (Del SQL Script)
        // ==================================================================
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->bigIncrements('conversacionid');
            $table->string('tipo', 20)->default('private'); // private | group
            $table->timestamps();
        });


        // ==================================================================
        // 9. TABLA: RESPUESTASMENSAJES (Del SQL Script)
        // ==================================================================
        Schema::create('conversacion_usuarios', function (Blueprint $table) {
            $table->bigIncrements('conversacion_usuarioid');

            $table->unsignedBigInteger('conversacionid');
            $table->unsignedBigInteger('usuarioid');

            // lectura por usuario (reemplaza leido/respondido)
            $table->timestamp('ultimo_leido')->nullable();

            $table->foreign('conversacionid')
                ->references('conversacionid')->on('conversaciones')
                ->onDelete('cascade');

            $table->foreign('usuarioid')
                ->references('usuarioid')->on('usuarios')
                ->onDelete('cascade');

            $table->unique(['conversacionid', 'usuarioid']);
        });

        Schema::create('mensajes', function (Blueprint $table) {
            $table->bigIncrements('mensajeid');

            $table->unsignedBigInteger('conversacionid'); 
            $table->unsignedBigInteger('usuarioid');      

            $table->string('asunto', 150); 
            $table->text('contenido');
            $table->timestamp('fechaenvio')->useCurrent();

            $table->foreign('conversacionid')
                ->references('conversacionid')->on('conversaciones')
                ->onDelete('cascade');

            $table->foreign('usuarioid')
                ->references('usuarioid')->on('usuarios')
                ->onDelete('cascade');

            $table->index(['conversacionid', 'fechaenvio']);
        });

        Schema::create('donacionesasignaciones', function (Blueprint $table) {
            $table->id('donacionasignacionid');
            $table->unsignedBigInteger('donacionid');
            $table->unsignedBigInteger('asignacionid');
            $table->decimal('montoasignado', 12, 2);
            $table->dateTime('fechaasignacion')->useCurrent();

            $table->foreign('donacionid')->references('donacionid')->on('donaciones')->onDelete('cascade');
            $table->foreign('asignacionid')->references('asignacionid')->on('asignaciones')->onDelete('cascade');
        });

        Schema::create('saldosdonaciones', function (Blueprint $table) {
            $table->id('saldoid');
            $table->unsignedBigInteger('donacionid')->unique();
            $table->decimal('montooriginal', 12, 2);
            $table->decimal('montoutilizado', 12, 2)->default(0);
            $table->decimal('saldodisponible', 12, 2);
            $table->dateTime('ultimaactualizacion')->useCurrent();

            $table->foreign('donacionid')->references('donacionid')->on('donaciones')->onDelete('cascade');
        });

        Schema::create('ext_categorias_productos', function (Blueprint $table) {
            $table->id('categoriaid');
            $table->unsignedInteger('idexterno')->unique(); 
            $table->string('nombre', 100);
            $table->timestamps();
        });

        Schema::create('ext_productos', function (Blueprint $table) {
            $table->id('productoid');
            $table->unsignedInteger('idexterno')->unique();
            $table->unsignedBigInteger('categoriaid')->nullable();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida', 50)->nullable();
            $table->timestamps();

            $table->foreign('categoriaid')->references('categoriaid')->on('ext_categorias_productos')->onDelete('set null');
        });

        Schema::create('ext_almacenes', function (Blueprint $table) {
            $table->id('almacenid');
            $table->unsignedInteger('idexterno')->unique();
            $table->string('nombre', 100);
            $table->text('direccion')->nullable();
            $table->string('latitud', 30)->nullable();
            $table->string('longitud', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('ext_estantes', function (Blueprint $table) {
            $table->id('estanteid');
            $table->unsignedInteger('idexterno')->unique();
            $table->unsignedBigInteger('almacenid');
            $table->string('codigo_estante', 50);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->foreign('almacenid')->references('almacenid')->on('ext_almacenes')->onDelete('cascade');
        });

        Schema::create('ext_espacios', function (Blueprint $table) {
            $table->id('espacioid');
            $table->unsignedInteger('idexterno')->unique();
            $table->unsignedBigInteger('estanteid');
            $table->string('codigo_espacio', 50);
            $table->string('estado', 30)->nullable();
            $table->timestamps();
            $table->foreign('estanteid')->references('estanteid')->on('ext_estantes')->onDelete('cascade');
        });
        Schema::create('trazabilidad_items', function (Blueprint $table) {
            $table->bigIncrements('trazabilidadid');
            $table->unsignedBigInteger('campaniaid')->nullable();
            $table->unsignedInteger('id_campana_externa')->nullable();
            $table->string('campania_nombre', 150)->nullable();
            $table->string('codigo_unico', 50);
            $table->unsignedInteger('id_donacion_externa');
            $table->unsignedInteger('id_detalle_externo');
            $table->unsignedBigInteger('productoid')->nullable();
            $table->string('nombre_producto', 150)->nullable();
            $table->string('categoria_producto', 100)->nullable();
            $table->string('talla', 20)->nullable();
            $table->string('genero', 20)->nullable();
            $table->integer('cantidad_donada')->nullable();
            $table->integer('cantidad_por_unidad')->nullable();
            $table->string('unidad_empaque', 50)->nullable();
            $table->integer('cantidad_ubicada')->nullable();
            $table->integer('cantidad_usada')->nullable();
            $table->timestamp('fecha_donacion')->nullable();
            $table->string('tipo_donacion', 20)->nullable();
            $table->string('nombre_donante', 150)->nullable();
            $table->unsignedBigInteger('almacenid')->nullable();
            $table->unsignedBigInteger('estanteid')->nullable();
            $table->unsignedBigInteger('espacioid')->nullable();
            $table->string('almacen_nombre', 100)->nullable();
            $table->string('estante_codigo', 50)->nullable();
            $table->string('espacio_codigo', 50)->nullable();
            $table->timestamp('fecha_ingreso_almacen')->nullable();
            $table->unsignedInteger('id_paquete_externo')->nullable();
            $table->string('codigo_paquete', 50)->nullable();
            $table->string('estado_paquete', 20)->nullable();
            $table->timestamp('fecha_creacion_paquete')->nullable();
            $table->unsignedInteger('id_solicitud_externa')->nullable();
            $table->string('codigo_solicitud', 100)->nullable();
            $table->string('estado_solicitud', 20)->nullable();
            $table->timestamp('fecha_solicitud')->nullable();
            $table->unsignedInteger('id_salida_externa')->nullable();
            $table->text('destino_final')->nullable();
            $table->timestamp('fecha_salida')->nullable();
            $table->string('estado_actual', 30)->nullable();
            $table->string('ubicacion_actual', 150)->nullable();
            $table->timestamp('fecha_ultima_actualizacion')->useCurrent();
            $table->timestamps();

            $table->foreign('campaniaid')->references('campaniaid')->on('campanias');
            $table->foreign('productoid')->references('productoid')->on('ext_productos');
            $table->foreign('almacenid')->references('almacenid')->on('ext_almacenes');
            $table->foreign('estanteid')->references('estanteid')->on('ext_estantes');
            $table->foreign('espacioid')->references('espacioid')->on('ext_espacios');
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('trazabilidad_items');
        Schema::dropIfExists('ext_espacios');
        Schema::dropIfExists('ext_estantes');
        Schema::dropIfExists('ext_almacenes');
        Schema::dropIfExists('ext_productos');
        Schema::dropIfExists('ext_categorias_productos');
        Schema::dropIfExists('saldosdonaciones');
        Schema::dropIfExists('donacionesasignaciones');
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('conversacion_usuarios');
        Schema::dropIfExists('conversaciones');
        Schema::dropIfExists('detallesasignacion');
        Schema::dropIfExists('asignaciones');
        Schema::dropIfExists('donaciones');
        Schema::dropIfExists('campanias');
        Schema::dropIfExists('estados');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('usuarios');
    }
};