-- =============================================================================
-- Modulo: logistica-transportacion-donaciones (schema logistica)
-- Alineado con database/migrations/2026_02_09_160000_logistica_module_sqlite_schema.php
-- Ejecutar conectado a: equipo05_unificado (despues de 01_extensions_and_schemas.sql)
-- =============================================================================

SET client_encoding = 'UTF8';
SET search_path TO logistica, public;

CREATE TABLE IF NOT EXISTS logistica.migrations (
    id          BIGSERIAL PRIMARY KEY,
    migration   VARCHAR(255) NOT NULL,
    batch       INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS logistica.users (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) NOT NULL,
    password        VARCHAR(255) NOT NULL,
    remember_token  VARCHAR(100),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);
CREATE UNIQUE INDEX IF NOT EXISTS log_users_email_unique ON logistica.users (email);

CREATE TABLE IF NOT EXISTS logistica.estado (
    id_estado       BIGSERIAL PRIMARY KEY,
    nombre_estado   VARCHAR(120) NOT NULL,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.solicitante (
    id_solicitante  BIGSERIAL PRIMARY KEY,
    nombre          VARCHAR(120) NOT NULL,
    apellido        VARCHAR(120),
    ci              VARCHAR(40) NOT NULL,
    telefono        VARCHAR(40),
    email           VARCHAR(120),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.destino (
    id_destino  BIGSERIAL PRIMARY KEY,
    comunidad   VARCHAR(120) NOT NULL,
    provincia   VARCHAR(120) NOT NULL,
    direccion   VARCHAR(255),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.solicitud (
    id_solicitud        BIGSERIAL PRIMARY KEY,
    estado              VARCHAR(40) NOT NULL DEFAULT 'pendiente',
    codigo_seguimiento  VARCHAR(64) NOT NULL,
    cantidad_personas   INTEGER NOT NULL DEFAULT 1,
    fecha_inicio        DATE,
    tipo_emergencia     VARCHAR(120),
    insumos_necesarios  TEXT,
    id_solicitante      BIGINT NOT NULL,
    id_destino          BIGINT NOT NULL,
    fecha_solicitud     DATE,
    aprobada            BOOLEAN NOT NULL DEFAULT FALSE,
    apoyoaceptado      BOOLEAN NOT NULL DEFAULT FALSE,
    fecha_necesidad     DATE,
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.paquete (
    id_paquete          BIGSERIAL PRIMARY KEY,
    id_solicitud        BIGINT,
    codigo              VARCHAR(64),
    ubicacion_actual    VARCHAR(255),
    fecha_creacion      TIMESTAMP(0) WITHOUT TIME ZONE,
    fecha_entrega       TIMESTAMP(0) WITHOUT TIME ZONE,
    estado_id           BIGINT,
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.historial_seguimiento_donaciones (
    id_historial            BIGSERIAL PRIMARY KEY,
    id_paquete              BIGINT,
    estado                  VARCHAR(80),
    fecha_actualizacion     TIMESTAMP(0) WITHOUT TIME ZONE,
    vehiculo_placa          VARCHAR(32),
    conductor_nombre        VARCHAR(120),
    conductor_ci            VARCHAR(40),
    created_at              TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at              TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.ubicacion (
    id_ubicacion    BIGSERIAL PRIMARY KEY,
    descripcion     VARCHAR(255),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.vehiculo (
    id_vehiculo BIGSERIAL PRIMARY KEY,
    placa       VARCHAR(32),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.conductor (
    id_conductor BIGSERIAL PRIMARY KEY,
    nombre       VARCHAR(120),
    apellido     VARCHAR(120),
    created_at   TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at   TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.tipo_vehiculo (
    id_tipo_vehiculo BIGSERIAL PRIMARY KEY,
    nombre           VARCHAR(120),
    created_at       TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at       TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.tipo_licencia (
    id_tipo_licencia BIGSERIAL PRIMARY KEY,
    nombre           VARCHAR(120),
    created_at       TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at       TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.tipo_emergencia (
    id_tipo_emergencia BIGSERIAL PRIMARY KEY,
    nombre             VARCHAR(120),
    created_at         TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at         TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.marca (
    id_marca   BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(120),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.reporte (
    id_reporte BIGSERIAL PRIMARY KEY,
    titulo     VARCHAR(200),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.usuario (
    id         BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(120),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS logistica.rol (
    id         BIGSERIAL PRIMARY KEY,
    nombre     VARCHAR(120),
    created_at TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE
);

COMMENT ON SCHEMA logistica IS 'Logistica transportacion donaciones (tablas integracion + espacio para FK futuras)';
