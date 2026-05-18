-- =============================================================================
-- Identidad global: un solo lugar para usuarios, roles Spatie y sesiones Laravel
-- Esquema: core
-- =============================================================================

SET client_encoding = 'UTF8';
SET search_path TO core, public;

CREATE TABLE IF NOT EXISTS core.usuarios (
    usuarioid       BIGSERIAL PRIMARY KEY,
    email           VARCHAR(100) NOT NULL UNIQUE,
    contrasena      VARCHAR(255) NOT NULL,
    nombre          VARCHAR(50) NOT NULL,
    apellido        VARCHAR(50) NOT NULL,
    telefono        VARCHAR(20),
    imagenurl       VARCHAR(255),
    activo          BOOLEAN NOT NULL DEFAULT TRUE,
    fecharegistro   TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS core.roles (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    guard_name      VARCHAR(255) NOT NULL,
    descripcion     VARCHAR(255),
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (name, guard_name)
);

CREATE TABLE IF NOT EXISTS core.permissions (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    guard_name      VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (name, guard_name)
);

CREATE TABLE IF NOT EXISTS core.model_has_roles (
    role_id         BIGINT NOT NULL REFERENCES core.roles (id) ON DELETE CASCADE,
    model_type      VARCHAR(255) NOT NULL,
    model_id        BIGINT NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type)
);
CREATE INDEX IF NOT EXISTS core_mhr_model_idx ON core.model_has_roles (model_id, model_type);

CREATE TABLE IF NOT EXISTS core.model_has_permissions (
    permission_id   BIGINT NOT NULL REFERENCES core.permissions (id) ON DELETE CASCADE,
    model_type      VARCHAR(255) NOT NULL,
    model_id        BIGINT NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type)
);
CREATE INDEX IF NOT EXISTS core_mhp_model_idx ON core.model_has_permissions (model_id, model_type);

CREATE TABLE IF NOT EXISTS core.role_has_permissions (
    permission_id   BIGINT NOT NULL REFERENCES core.permissions (id) ON DELETE CASCADE,
    role_id         BIGINT NOT NULL REFERENCES core.roles (id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, role_id)
);

CREATE TABLE IF NOT EXISTS core.password_reset_tokens (
    email       VARCHAR(255) PRIMARY KEY,
    token       VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE IF NOT EXISTS core.sessions (
    id              VARCHAR(255) PRIMARY KEY,
    user_id         BIGINT REFERENCES core.usuarios (usuarioid) ON DELETE SET NULL,
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    payload         TEXT NOT NULL,
    last_activity   INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS core_sessions_user_id_index ON core.sessions (user_id);
CREATE INDEX IF NOT EXISTS core_sessions_last_activity_index ON core.sessions (last_activity);

-- Caché Laravel (Spatie Permission y CACHE_STORE=database)
CREATE TABLE IF NOT EXISTS core.cache (
    key         VARCHAR(255) NOT NULL PRIMARY KEY,
    value       TEXT NOT NULL,
    expiration  INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS core_cache_expiration_index ON core.cache (expiration);

CREATE TABLE IF NOT EXISTS core.cache_locks (
    key         VARCHAR(255) NOT NULL PRIMARY KEY,
    owner       VARCHAR(255) NOT NULL,
    expiration  INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS core_cache_locks_expiration_index ON core.cache_locks (expiration);
