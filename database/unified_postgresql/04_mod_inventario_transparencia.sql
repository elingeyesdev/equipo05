-- =============================================================================
-- Modulo: inventario / transparencia donaciones (schema inventario)
-- Fuente: postgresql_schema_dbeaver.sql — las tablas se crean en el esquema inventario
-- =============================================================================

-- Tablas de transparencia/campanas (dominio distinto al modulo almacen-inventario).
SET search_path TO transparencia, public;
-- =============================================================================
-- Esquema PostgreSQL â€” Transparencia donaciones / voluntarios (Laravel)
-- Generado a partir de: database/migrations/*.php y modelos Eloquent.
--
-- Uso en DBeaver:
--   1) Crear una base vacÃ­a (ej. CREATE DATABASE donaciones;)
--   2) Conectarse a esa base y ejecutar este script completo.
--
-- Incluye:
--   - Tablas del dominio (usuarios, campaÃ±as, donaciones, mensajerÃ­a, etc.)
--   - IntegraciÃ³n externa (ext_*, trazabilidad_items, recursos_externos)
--   - Spatie Permission (roles, permissions, pivotes)
--   - Sesiones Laravel, cachÃ©, colas (jobs / failed_jobs) segÃºn .env.example
--   - Tabla respuestasmensajes (usada en cÃ³digo; no tenÃ­a migraciÃ³n)
--   - Columna mensajes.leido (usada en Dashboard/Mensajes; faltaba en migraciÃ³n)
-- =============================================================================

SET client_encoding = 'UTF8';

-- -----------------------------------------------------------------------------
-- Limpieza (opcional): descomentar si necesitas recrear desde cero
-- -----------------------------------------------------------------------------
/*
DROP TABLE IF EXISTS failed_jobs CASCADE;
DROP TABLE IF EXISTS jobs CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS recursos_externos CASCADE;
DROP TABLE IF EXISTS ext_paquetes CASCADE;
DROP TABLE IF EXISTS trazabilidad_items CASCADE;
DROP TABLE IF EXISTS ext_espacios CASCADE;
DROP TABLE IF EXISTS ext_estantes CASCADE;
DROP TABLE IF EXISTS ext_almacenes CASCADE;
DROP TABLE IF EXISTS ext_productos CASCADE;
DROP TABLE IF EXISTS ext_categorias_productos CASCADE;
DROP TABLE IF EXISTS saldosdonaciones CASCADE;
DROP TABLE IF EXISTS donacionesasignaciones CASCADE;
DROP TABLE IF EXISTS respuestasmensajes CASCADE;
DROP TABLE IF EXISTS mensajes CASCADE;
DROP TABLE IF EXISTS conversacion_usuarios CASCADE;
DROP TABLE IF EXISTS conversaciones CASCADE;
DROP TABLE IF EXISTS detallesasignacion CASCADE;
DROP TABLE IF EXISTS asignaciones CASCADE;
DROP TABLE IF EXISTS donaciones CASCADE;
DROP TABLE IF EXISTS campanias CASCADE;
DROP TABLE IF EXISTS estados CASCADE;
DROP TABLE IF EXISTS model_has_permissions CASCADE;
DROP TABLE IF EXISTS model_has_roles CASCADE;
DROP TABLE IF EXISTS role_has_permissions CASCADE;
DROP TABLE IF EXISTS permissions CASCADE;
DROP TABLE IF EXISTS roles CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS usuarios CASCADE;
*/

-- Usuarios y Spatie: database/unified_postgresql/00_core_auth.sql (esquema core)

-- =============================================================================
-- 1. Estados, campañas, donaciones, asignaciones
-- =============================================================================

CREATE TABLE estados (
    estadoid        BIGSERIAL PRIMARY KEY,
    nombre          VARCHAR(50) NOT NULL,
    descripcion     VARCHAR(255)
);

CREATE TABLE campanias (
    campaniaid          BIGSERIAL PRIMARY KEY,
    idexterno           INTEGER UNIQUE,
    titulo              VARCHAR(100) NOT NULL,
    descripcion         TEXT NOT NULL,
    fechainicio         DATE NOT NULL,
    fechafin            DATE,
    metarecaudacion     NUMERIC(12, 2) NOT NULL,
    montorecaudado      NUMERIC(12, 2) NOT NULL DEFAULT 0,
    usuarioidcreador    BIGINT NOT NULL REFERENCES core.usuarios (usuarioid),
    activa              BOOLEAN NOT NULL DEFAULT TRUE,
    imagenurl           VARCHAR(255),
    fechacreacion       TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE donaciones (
    donacionid      BIGSERIAL PRIMARY KEY,
    idexterno       INTEGER UNIQUE,
    usuarioid       BIGINT REFERENCES core.usuarios (usuarioid),
    campaniaid      BIGINT REFERENCES campanias (campaniaid),
    monto           NUMERIC(12, 2) NOT NULL DEFAULT 0,
    tipodonacion    VARCHAR(20) NOT NULL,
    descripcion     TEXT,
    fechadonacion   TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estadoid        BIGINT NOT NULL DEFAULT 1 REFERENCES estados (estadoid),
    esanonima       BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE asignaciones (
    asignacionid        BIGSERIAL PRIMARY KEY,
    campaniaid          BIGINT NOT NULL REFERENCES campanias (campaniaid),
    descripcion         VARCHAR(255) NOT NULL,
    monto               NUMERIC(12, 2) NOT NULL,
    fechaasignacion     TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    imagenurl           VARCHAR(255),
    usuarioid           BIGINT NOT NULL REFERENCES core.usuarios (usuarioid),
    comprobante         VARCHAR(255)
);

CREATE TABLE detallesasignacion (
    detalleid       BIGSERIAL PRIMARY KEY,
    asignacionid   BIGINT NOT NULL REFERENCES asignaciones (asignacionid) ON DELETE CASCADE,
    concepto        VARCHAR(100) NOT NULL,
    cantidad        INTEGER NOT NULL,
    preciounitario  NUMERIC(10, 2) NOT NULL,
    imagenurl       VARCHAR(255)
);

-- =============================================================================
-- 3. MensajerÃ­a (conversaciones, mensajes, respuestas)
-- =============================================================================

CREATE TABLE conversaciones (
    conversacionid  BIGSERIAL PRIMARY KEY,
    tipo            VARCHAR(20) NOT NULL DEFAULT 'private',
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE conversacion_usuarios (
    conversacion_usuarioid  BIGSERIAL PRIMARY KEY,
    conversacionid          BIGINT NOT NULL REFERENCES conversaciones (conversacionid) ON DELETE CASCADE,
    usuarioid               BIGINT NOT NULL REFERENCES core.usuarios (usuarioid) ON DELETE CASCADE,
    ultimo_leido            TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (conversacionid, usuarioid)
);

CREATE TABLE mensajes (
    mensajeid       BIGSERIAL PRIMARY KEY,
    conversacionid  BIGINT NOT NULL REFERENCES conversaciones (conversacionid) ON DELETE CASCADE,
    usuarioid       BIGINT NOT NULL REFERENCES core.usuarios (usuarioid) ON DELETE CASCADE,
    asunto          VARCHAR(150) NOT NULL,
    contenido       TEXT NOT NULL,
    fechaenvio      TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    leido           BOOLEAN NOT NULL DEFAULT FALSE
);
CREATE INDEX mensajes_conversacionid_fechaenvio_index ON mensajes (conversacionid, fechaenvio);

CREATE TABLE respuestasmensajes (
    respuestaid     BIGSERIAL PRIMARY KEY,
    mensajeid       BIGINT NOT NULL REFERENCES mensajes (mensajeid) ON DELETE CASCADE,
    usuarioid       BIGINT NOT NULL REFERENCES core.usuarios (usuarioid),
    contenido       TEXT NOT NULL,
    fecharespuesta  TIMESTAMP(0) WITHOUT TIME ZONE
);

-- =============================================================================
-- 4. Donacionesâ€“asignaciones y saldos
-- =============================================================================

CREATE TABLE donacionesasignaciones (
    donacionasignacionid    BIGSERIAL PRIMARY KEY,
    donacionid              BIGINT NOT NULL REFERENCES donaciones (donacionid) ON DELETE CASCADE,
    asignacionid           BIGINT NOT NULL REFERENCES asignaciones (asignacionid) ON DELETE CASCADE,
    montoasignado           NUMERIC(12, 2) NOT NULL,
    fechaasignacion         TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE saldosdonaciones (
    saldoid                 BIGSERIAL PRIMARY KEY,
    donacionid              BIGINT NOT NULL UNIQUE REFERENCES donaciones (donacionid) ON DELETE CASCADE,
    montooriginal           NUMERIC(12, 2) NOT NULL,
    montoutilizado          NUMERIC(12, 2) NOT NULL DEFAULT 0,
    saldodisponible         NUMERIC(12, 2) NOT NULL,
    ultimaactualizacion     TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =============================================================================
-- 5. CatÃ¡logo externo (gateway) y trazabilidad
-- =============================================================================

CREATE TABLE ext_categorias_productos (
    categoriaid     BIGSERIAL PRIMARY KEY,
    idexterno       INTEGER NOT NULL UNIQUE,
    nombre          VARCHAR(100) NOT NULL,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE ext_productos (
    productoid      BIGSERIAL PRIMARY KEY,
    idexterno       INTEGER NOT NULL UNIQUE,
    categoriaid     BIGINT REFERENCES ext_categorias_productos (categoriaid) ON DELETE SET NULL,
    nombre          VARCHAR(100) NOT NULL,
    descripcion     TEXT,
    unidad_medida   VARCHAR(50),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE ext_almacenes (
    almacenid       BIGSERIAL PRIMARY KEY,
    idexterno       INTEGER NOT NULL UNIQUE,
    nombre          VARCHAR(100) NOT NULL,
    direccion       TEXT,
    latitud         VARCHAR(30),
    longitud        VARCHAR(30),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE ext_estantes (
    estanteid       BIGSERIAL PRIMARY KEY,
    idexterno       INTEGER NOT NULL UNIQUE,
    almacenid       BIGINT NOT NULL REFERENCES ext_almacenes (almacenid) ON DELETE CASCADE,
    codigo_estante  VARCHAR(50) NOT NULL,
    descripcion     TEXT,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE ext_espacios (
    espacioid       BIGSERIAL PRIMARY KEY,
    idexterno       INTEGER NOT NULL UNIQUE,
    estanteid       BIGINT NOT NULL REFERENCES ext_estantes (estanteid) ON DELETE CASCADE,
    codigo_espacio  VARCHAR(50) NOT NULL,
    estado          VARCHAR(30),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE trazabilidad_items (
    trazabilidadid              BIGSERIAL PRIMARY KEY,
    campaniaid                  BIGINT REFERENCES campanias (campaniaid),
    id_campana_externa          INTEGER,
    campania_nombre             VARCHAR(150),
    codigo_unico                VARCHAR(50) NOT NULL,
    id_donacion_externa         INTEGER NOT NULL,
    id_detalle_externo          INTEGER NOT NULL,
    productoid                  BIGINT REFERENCES ext_productos (productoid),
    nombre_producto             VARCHAR(150),
    categoria_producto          VARCHAR(100),
    talla                       VARCHAR(20),
    genero                      VARCHAR(20),
    cantidad_donada             INTEGER,
    cantidad_por_unidad         INTEGER,
    unidad_empaque              VARCHAR(50),
    cantidad_ubicada            INTEGER,
    cantidad_usada              INTEGER,
    fecha_donacion              TIMESTAMP(0) WITHOUT TIME ZONE,
    tipo_donacion               VARCHAR(20),
    nombre_donante              VARCHAR(150),
    almacenid                   BIGINT REFERENCES ext_almacenes (almacenid),
    estanteid                   BIGINT REFERENCES ext_estantes (estanteid),
    espacioid                   BIGINT REFERENCES ext_espacios (espacioid),
    almacen_nombre              VARCHAR(100),
    estante_codigo              VARCHAR(50),
    espacio_codigo              VARCHAR(50),
    fecha_ingreso_almacen       TIMESTAMP(0) WITHOUT TIME ZONE,
    id_paquete_externo          INTEGER,
    codigo_paquete              VARCHAR(50),
    estado_paquete              VARCHAR(20),
    fecha_creacion_paquete      TIMESTAMP(0) WITHOUT TIME ZONE,
    id_solicitud_externa        INTEGER,
    codigo_solicitud            VARCHAR(100),
    estado_solicitud            VARCHAR(20),
    fecha_solicitud             TIMESTAMP(0) WITHOUT TIME ZONE,
    id_salida_externa           INTEGER,
    destino_final               TEXT,
    fecha_salida                TIMESTAMP(0) WITHOUT TIME ZONE,
    estado_actual               VARCHAR(30),
    ubicacion_actual            VARCHAR(150),
    fecha_ultima_actualizacion  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at                  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at                  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE ext_paquetes (
    id              BIGSERIAL PRIMARY KEY,
    codigo_paquete VARCHAR(255) NOT NULL UNIQUE,
    estado          VARCHAR(255),
    fecha_creacion  TIMESTAMP(0) WITHOUT TIME ZONE,
    datos_gateway   JSONB,
    ultimo_sync     TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE recursos_externos (
    id                  BIGSERIAL PRIMARY KEY,
    tipo                VARCHAR(255) NOT NULL,
    gateway_id          BIGINT NOT NULL,
    identificador       VARCHAR(255) NOT NULL,
    datos_extra         JSONB,
    response_detalle    TEXT,
    detalle_cached_at   TIMESTAMP(0) WITHOUT TIME ZONE,
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (tipo, gateway_id)
);
CREATE INDEX recursos_externos_tipo_index ON recursos_externos (tipo);
CREATE INDEX recursos_externos_identificador_index ON recursos_externos (identificador);

-- =============================================================================
-- 6. SesiÃ³n, cachÃ© y colas (Laravel)
-- =============================================================================

CREATE TABLE sessions (
    id              VARCHAR(255) PRIMARY KEY,
    user_id         BIGINT,
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    payload         TEXT NOT NULL,
    last_activity   INTEGER NOT NULL
);
CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);

CREATE TABLE cache (
    key             VARCHAR(255) PRIMARY KEY,
    value           TEXT NOT NULL,
    expiration      INTEGER NOT NULL
);
CREATE INDEX cache_expiration_index ON cache (expiration);

CREATE TABLE cache_locks (
    key             VARCHAR(255) PRIMARY KEY,
    owner           VARCHAR(255) NOT NULL,
    expiration      INTEGER NOT NULL
);
CREATE INDEX cache_locks_expiration_index ON cache_locks (expiration);

CREATE TABLE jobs (
    id              BIGSERIAL PRIMARY KEY,
    queue           VARCHAR(255) NOT NULL,
    payload         TEXT NOT NULL,
    attempts        SMALLINT NOT NULL,
    reserved_at     INTEGER,
    available_at    INTEGER NOT NULL,
    created_at      INTEGER NOT NULL
);
CREATE INDEX jobs_queue_index ON jobs (queue);

CREATE TABLE failed_jobs (
    id              BIGSERIAL PRIMARY KEY,
    uuid            VARCHAR(255) NOT NULL UNIQUE,
    connection      TEXT NOT NULL,
    queue           TEXT NOT NULL,
    payload         TEXT NOT NULL,
    exception       TEXT NOT NULL,
    failed_at       TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE password_reset_tokens (
    email           VARCHAR(255) PRIMARY KEY,
    token           VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

-- =============================================================================
-- Datos mÃ­nimos: donaciones usa estadoid DEFAULT 1; el primer INSERT recibe id 1.
-- =============================================================================
INSERT INTO estados (nombre, descripcion) VALUES ('Pendiente', 'Estado por defecto para nuevas donaciones');

-- =============================================================================
-- Fin del esquema. Roles y usuarios de prueba: php artisan db:seed
-- =============================================================================

