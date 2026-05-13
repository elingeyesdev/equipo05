-- =============================================================================
-- Modulo: seguimiento-voluntarios-comunarios (schema seguimiento)
-- Alineado con database/migrations/2026_02_09_160001_seguimiento_module_sqlite_schema.php
-- Ejecutar conectado a: equipo05_unificado (despues de 01_extensions_and_schemas.sql)
-- =============================================================================

SET client_encoding = 'UTF8';
SET search_path TO seguimiento, public;

CREATE TABLE IF NOT EXISTS seguimiento.migrations (
    id          BIGSERIAL PRIMARY KEY,
    migration   VARCHAR(255) NOT NULL,
    batch       INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS seguimiento.usuario (
    id_usuario      BIGSERIAL PRIMARY KEY,
    nombre          VARCHAR(150),
    apellido        VARCHAR(150),
    email           VARCHAR(150),
    activo          BOOLEAN NOT NULL DEFAULT TRUE,
    administrador   BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.evaluacion (
    id_evaluacion   BIGSERIAL PRIMARY KEY,
    id_usuario      BIGINT,
    titulo          VARCHAR(200),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.evaluacion_tokens (
    id          BIGSERIAL PRIMARY KEY,
    token       VARCHAR(128),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.capacitacion (
    id_capacitacion BIGSERIAL PRIMARY KEY,
    nombre          VARCHAR(200),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.necesidad (
    id_necesidad BIGSERIAL PRIMARY KEY,
    nombre       VARCHAR(200),
    created_at   TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at   TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.solicitudes_ayuda (
    id          BIGSERIAL PRIMARY KEY,
    estado      VARCHAR(80),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.chat_mensajes (
    id          BIGSERIAL PRIMARY KEY,
    mensaje     TEXT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.universidad (
    id_universidad BIGSERIAL PRIMARY KEY,
    nombre         VARCHAR(200),
    created_at     TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at     TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS seguimiento.consultas (
    id          BIGSERIAL PRIMARY KEY,
    asunto      VARCHAR(200),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

COMMENT ON SCHEMA seguimiento IS 'Seguimiento voluntarios comunarios';
