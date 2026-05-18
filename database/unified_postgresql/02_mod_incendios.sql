-- =============================================================================
-- Modulo: monitoreo-incendios-simulacion (schema incendios)
-- Portado desde SQLite / migraciones Laravel — PostgreSQL
-- Ejecutar conectado a: equipo05_unificado (despues de 01_)
-- =============================================================================

SET client_encoding = 'UTF8';

CREATE TABLE incendios.migrations (
    id          BIGSERIAL PRIMARY KEY,
    migration   VARCHAR(255) NOT NULL,
    batch       INTEGER NOT NULL
);

-- Usuarios: core.usuarios (00_core_auth.sql)

CREATE TABLE incendios.password_reset_tokens (
    email       VARCHAR(255) PRIMARY KEY,
    token       VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE incendios.sessions (
    id              VARCHAR(255) PRIMARY KEY,
    user_id         BIGINT,
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    payload         TEXT NOT NULL,
    last_activity   INTEGER NOT NULL
);
CREATE INDEX inc_sessions_user_id_index ON incendios.sessions (user_id);
CREATE INDEX inc_sessions_last_activity_index ON incendios.sessions (last_activity);

CREATE TABLE incendios.cache (
    key         VARCHAR(255) PRIMARY KEY,
    value       TEXT NOT NULL,
    expiration  INTEGER NOT NULL
);

CREATE TABLE incendios.cache_locks (
    key         VARCHAR(255) PRIMARY KEY,
    owner       VARCHAR(255) NOT NULL,
    expiration  INTEGER NOT NULL
);

CREATE TABLE incendios.jobs (
    id            BIGSERIAL PRIMARY KEY,
    queue         VARCHAR(255) NOT NULL,
    payload       TEXT NOT NULL,
    attempts      SMALLINT NOT NULL,
    reserved_at   INTEGER,
    available_at  INTEGER NOT NULL,
    created_at    INTEGER NOT NULL
);
CREATE INDEX inc_jobs_queue_index ON incendios.jobs (queue);

CREATE TABLE incendios.job_batches (
    id              VARCHAR(255) PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    total_jobs      INTEGER NOT NULL,
    pending_jobs    INTEGER NOT NULL,
    failed_jobs     INTEGER NOT NULL,
    failed_job_ids  TEXT NOT NULL,
    options         TEXT,
    cancelled_at    INTEGER,
    created_at      INTEGER NOT NULL,
    finished_at     INTEGER
);

CREATE TABLE incendios.failed_jobs (
    id          BIGSERIAL PRIMARY KEY,
    uuid        VARCHAR(255) NOT NULL,
    connection  TEXT NOT NULL,
    queue       TEXT NOT NULL,
    payload     TEXT NOT NULL,
    exception   TEXT NOT NULL,
    failed_at   TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX inc_failed_jobs_uuid_unique ON incendios.failed_jobs (uuid);

CREATE TABLE incendios.tipo_biomasa (
    id                      BIGSERIAL PRIMARY KEY,
    tipo_biomasa           VARCHAR(255) NOT NULL,
    created_at              TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at              TIMESTAMP(0) WITHOUT TIME ZONE,
    color                   VARCHAR(255) NOT NULL DEFAULT '#4CAF50',
    modificador_intensidad  NUMERIC(8, 2) NOT NULL DEFAULT 1
);
CREATE UNIQUE INDEX inc_tipo_biomasa_unique ON incendios.tipo_biomasa (tipo_biomasa);

CREATE TABLE incendios.focos_incendios (
    id          BIGSERIAL PRIMARY KEY,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    fecha       TIMESTAMP(0) WITHOUT TIME ZONE,
    ubicacion   VARCHAR(255),
    coordenadas TEXT,
    intensidad  DOUBLE PRECISION
);
CREATE INDEX inc_focos_fecha_index ON incendios.focos_incendios (fecha);
CREATE INDEX inc_focos_intensidad_index ON incendios.focos_incendios (intensidad);
CREATE INDEX inc_focos_created_at_index ON incendios.focos_incendios (created_at);
CREATE INDEX inc_focos_fecha_intensidad_index ON incendios.focos_incendios (fecha, intensidad);

CREATE TABLE incendios.foco_tracks (
    id                BIGSERIAL PRIMARY KEY,
    foco_incendio_id  BIGINT NOT NULL REFERENCES incendios.focos_incendios (id) ON DELETE CASCADE,
    recorded_at       TIMESTAMP(0) WITHOUT TIME ZONE,
    coordinates       TEXT NOT NULL,
    intensidad        DOUBLE PRECISION,
    created_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at        TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE incendios.administradores (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES core.usuarios (usuarioid) ON DELETE CASCADE,
    departamento    VARCHAR(255),
    nivel_acceso    VARCHAR(255) NOT NULL DEFAULT 'basico',
    activo          BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);
CREATE UNIQUE INDEX inc_administradores_user_id_unique ON incendios.administradores (user_id);

CREATE TABLE incendios.voluntarios (
    id          BIGSERIAL PRIMARY KEY,
    user_id     BIGINT NOT NULL REFERENCES core.usuarios (usuarioid) ON DELETE CASCADE,
    direccion   VARCHAR(255),
    ciudad      VARCHAR(255),
    zona        VARCHAR(255),
    notas       TEXT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);
CREATE UNIQUE INDEX inc_voluntarios_user_id_unique ON incendios.voluntarios (user_id);

CREATE TABLE incendios.simulaciones (
    id                        BIGSERIAL PRIMARY KEY,
    created_at                TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at                TIMESTAMP(0) WITHOUT TIME ZONE,
    nombre                    VARCHAR(255) NOT NULL,
    fecha                     TIMESTAMP(0) WITHOUT TIME ZONE,
    duracion                  INTEGER,
    focos_activos             INTEGER NOT NULL DEFAULT 0,
    num_voluntarios_enviados  INTEGER NOT NULL DEFAULT 0,
    estado                    VARCHAR(255) NOT NULL DEFAULT 'pendiente',
    admin_id                  BIGINT REFERENCES incendios.administradores (id) ON DELETE SET NULL,
    temperature               NUMERIC(10, 4),
    humidity                  NUMERIC(10, 4),
    wind_speed                NUMERIC(10, 4),
    wind_direction            INTEGER,
    simulation_speed          NUMERIC(10, 4) NOT NULL DEFAULT 1,
    fire_risk                 INTEGER,
    map_center_lat            NUMERIC(12, 8),
    map_center_lng            NUMERIC(12, 8),
    initial_fires             TEXT,
    mitigation_strategies     TEXT,
    auto_stopped              BOOLEAN NOT NULL DEFAULT FALSE,
    ci_usuario                VARCHAR(255),
    deleted_at                TIMESTAMP(0) WITHOUT TIME ZONE,
    public                    BOOLEAN NOT NULL DEFAULT FALSE
);
CREATE INDEX inc_simulaciones_ci_usuario_index ON incendios.simulaciones (ci_usuario);
CREATE INDEX inc_simulaciones_public_index ON incendios.simulaciones (public);

CREATE TABLE incendios.foco_simulacion (
    id                BIGSERIAL PRIMARY KEY,
    foco_incendio_id  BIGINT NOT NULL REFERENCES incendios.focos_incendios (id) ON DELETE CASCADE,
    simulacion_id     BIGINT NOT NULL REFERENCES incendios.simulaciones (id) ON DELETE CASCADE,
    agregado_at       TIMESTAMP(0) WITHOUT TIME ZONE,
    activo            BOOLEAN NOT NULL DEFAULT TRUE,
    created_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (foco_incendio_id, simulacion_id)
);

CREATE TABLE incendios.predictions (
    id                BIGSERIAL PRIMARY KEY,
    predicted_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    path              TEXT,
    meta              TEXT,
    created_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    foco_incendio_id  BIGINT REFERENCES incendios.focos_incendios (id) ON DELETE CASCADE,
    user_id           BIGINT REFERENCES core.usuarios (usuarioid) ON DELETE SET NULL,
    ci_usuario        VARCHAR(255),
    deleted_at        TIMESTAMP(0) WITHOUT TIME ZONE
);
CREATE INDEX inc_predictions_ci_usuario_index ON incendios.predictions (ci_usuario);

CREATE TABLE incendios.simulation_fire_history (
    id             BIGSERIAL PRIMARY KEY,
    simulacion_id  BIGINT NOT NULL REFERENCES incendios.simulaciones (id) ON DELETE CASCADE,
    fire_id        VARCHAR(255) NOT NULL,
    time_step      INTEGER NOT NULL,
    lat            NUMERIC(12, 8) NOT NULL,
    lng            NUMERIC(12, 8) NOT NULL,
    intensity      NUMERIC(12, 4) NOT NULL,
    spread         NUMERIC(12, 4) NOT NULL,
    active         BOOLEAN NOT NULL DEFAULT TRUE,
    created_at     TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at     TIMESTAMP(0) WITHOUT TIME ZONE
);
CREATE INDEX inc_sim_fire_hist_sim_time_index ON incendios.simulation_fire_history (simulacion_id, time_step);

CREATE TABLE incendios.biomasas (
    id                BIGSERIAL PRIMARY KEY,
    created_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    area_m2           BIGINT,
    densidad          VARCHAR(255) NOT NULL DEFAULT 'media',
    ubicacion         VARCHAR(255),
    descripcion       TEXT,
    user_id           BIGINT REFERENCES core.usuarios (usuarioid) ON DELETE SET NULL,
    tipo_biomasa_id   BIGINT REFERENCES incendios.tipo_biomasa (id) ON DELETE SET NULL,
    fecha_reporte     DATE,
    coordenadas       TEXT,
    perimetro_m       NUMERIC(14, 4),
    estado            VARCHAR(255) NOT NULL DEFAULT 'pendiente',
    motivo_rechazo    TEXT,
    aprobada_por      BIGINT REFERENCES core.usuarios (usuarioid) ON DELETE SET NULL,
    fecha_revision    TIMESTAMP(0) WITHOUT TIME ZONE,
    ci_usuario        VARCHAR(255),
    deleted_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    CONSTRAINT inc_biomasas_estado_chk CHECK (estado IN ('pendiente', 'aprobada', 'rechazada'))
);
CREATE INDEX inc_biomasas_ci_usuario_index ON incendios.biomasas (ci_usuario);

CREATE TABLE incendios.permissions (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    guard_name  VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (name, guard_name)
);

CREATE TABLE incendios.roles (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    guard_name  VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    UNIQUE (name, guard_name)
);

CREATE TABLE incendios.model_has_permissions (
    permission_id BIGINT NOT NULL REFERENCES incendios.permissions (id) ON DELETE CASCADE,
    model_type      VARCHAR(255) NOT NULL,
    model_id        BIGINT NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type)
);
CREATE INDEX inc_mhp_model_idx ON incendios.model_has_permissions (model_id, model_type);

CREATE TABLE incendios.model_has_roles (
    role_id     BIGINT NOT NULL REFERENCES incendios.roles (id) ON DELETE CASCADE,
    model_type  VARCHAR(255) NOT NULL,
    model_id    BIGINT NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type)
);
CREATE INDEX inc_mhr_model_idx ON incendios.model_has_roles (model_id, model_type);

CREATE TABLE incendios.role_has_permissions (
    permission_id BIGINT NOT NULL REFERENCES incendios.permissions (id) ON DELETE CASCADE,
    role_id         BIGINT NOT NULL REFERENCES incendios.roles (id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, role_id)
);

CREATE TABLE incendios.personal_access_tokens (
    id              BIGSERIAL PRIMARY KEY,
    tokenable_type  VARCHAR(255) NOT NULL,
    tokenable_id    BIGINT NOT NULL,
    name            VARCHAR(255) NOT NULL,
    token           VARCHAR(64) NOT NULL UNIQUE,
    abilities       TEXT,
    last_used_at    TIMESTAMP(0) WITHOUT TIME ZONE,
    expires_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
);
CREATE INDEX inc_pat_tokenable_idx ON incendios.personal_access_tokens (tokenable_type, tokenable_id);
CREATE INDEX inc_pat_expires_at_idx ON incendios.personal_access_tokens (expires_at);
