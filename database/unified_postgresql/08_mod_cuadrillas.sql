-- =============================================================================
-- Modulo: cuadrillas-incendios-kardex-cursos (schema cuadrillas)
-- Alineado con database/migrations/2026_02_09_160002_cuadrillas_module_sqlite_schema.php
-- Ejecutar conectado a: equipo05_unificado (despues de 01_extensions_and_schemas.sql)
-- Requiere postgis (ya creado en 01_extensions_and_schemas.sql) si mas adelante se agregan columnas geography
-- =============================================================================

SET client_encoding = 'UTF8';
SET search_path TO cuadrillas, public;

CREATE TABLE IF NOT EXISTS cuadrillas.migrations (
    id          BIGSERIAL PRIMARY KEY,
    migration   VARCHAR(255) NOT NULL,
    batch       INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS cuadrillas.reporte (
    id_reporte           BIGSERIAL PRIMARY KEY,
    titulo               VARCHAR(255),
    nombre_reportante    VARCHAR(200),
    telefono_contacto    VARCHAR(20),
    fecha_hora           TIMESTAMP(0) WITHOUT TIME ZONE,
    nombre_lugar         VARCHAR(200),
    latitud              NUMERIC(10, 8),
    longitud             NUMERIC(11, 8),
    tipo_incidente_id    BIGINT,
    gravedad_id          BIGINT,
    comentario_adicional TEXT,
    cant_bomberos        INTEGER DEFAULT 0,
    cant_paramedicos     INTEGER DEFAULT 0,
    cant_veterinarios    INTEGER DEFAULT 0,
    cant_autoridades     INTEGER DEFAULT 0,
    estado_id            BIGINT,
    created_at           TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at           TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.reporte_incendio (
    id_reporte_incendio BIGSERIAL PRIMARY KEY,
    titulo              VARCHAR(255),
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.foco_calor (
    id_foco_calor BIGSERIAL PRIMARY KEY,
    latitud       NUMERIC(10, 8),
    longitud      NUMERIC(11, 8),
    created_at    TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at    TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.equipo (
    id_equipo            BIGSERIAL PRIMARY KEY,
    nombre               VARCHAR(200),
    cantidad_integrantes INTEGER DEFAULT 0,
    latitud              NUMERIC(10, 8),
    longitud             NUMERIC(11, 8),
    estado_id            BIGINT,
    created_at           TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at           TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.recurso (
    id_recurso BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(200),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.noticia (
    id_noticia  BIGSERIAL PRIMARY KEY,
    titulo      VARCHAR(500),
    descripcion TEXT,
    url         VARCHAR(500),
    image       VARCHAR(500),
    date        TIMESTAMP(0) WITHOUT TIME ZONE,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.curso (
    id_curso    BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(200),
    descripcion TEXT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.inscrito (
    id_inscrito BIGSERIAL PRIMARY KEY,
    id_curso    BIGINT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.comunario (
    id_comunario BIGSERIAL PRIMARY KEY,
    nombre       VARCHAR(200),
    created_at   TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at   TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.usuario (
    id_usuario BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(200),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.role (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(120),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.genero (
    id_genero  BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(80),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.tipo_sangre (
    id_tipo_sangre BIGSERIAL PRIMARY KEY,
    nombre         VARCHAR(80),
    created_at     TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at     TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.nivel_entrenamiento (
    id_nivel_entrenamiento BIGSERIAL PRIMARY KEY,
    nombre                 VARCHAR(120),
    created_at             TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at             TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.nivel_gravedad (
    id_nivel_gravedad BIGSERIAL PRIMARY KEY,
    nombre             VARCHAR(120),
    created_at         TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at         TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.tipo_incidente (
    id_tipo_incidente BIGSERIAL PRIMARY KEY,
    nombre            VARCHAR(120),
    created_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at        TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.tipo_recurso (
    id_tipo_recurso BIGSERIAL PRIMARY KEY,
    nombre          VARCHAR(120),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.condicion_climatica (
    id_condicion_climatica BIGSERIAL PRIMARY KEY,
    nombre                 VARCHAR(120),
    created_at             TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at             TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.estado_sistema (
    id_estado_sistema BIGSERIAL PRIMARY KEY,
    nombre            VARCHAR(120),
    codigo            VARCHAR(120),
    color             VARCHAR(20),
    tabla             VARCHAR(120),
    created_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at        TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.kardex (
    id_kardex   BIGSERIAL PRIMARY KEY,
    descripcion VARCHAR(255),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS cuadrillas.consultas (
    id          BIGSERIAL PRIMARY KEY,
    asunto      VARCHAR(200),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

COMMENT ON SCHEMA cuadrillas IS 'Cuadrillas incendios kardex cursos';
