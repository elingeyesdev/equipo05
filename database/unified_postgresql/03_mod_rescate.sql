-- =============================================================================
-- Modulo: rescate-animales-silvestres (schema rescate)
-- Portado desde SQLite / migraciones Laravel — PostgreSQL
-- Nota: animal_files <-> releases es circular; se resuelve con ALTER al final.
-- =============================================================================

SET client_encoding = 'UTF8';

CREATE TABLE rescate.migrations (
    id          BIGSERIAL PRIMARY KEY,
    migration   VARCHAR(255) NOT NULL,
    batch       INTEGER NOT NULL
);

-- Usuarios: core.usuarios (00_core_auth.sql)

CREATE TABLE rescate.password_reset_tokens (
    email       VARCHAR(255) PRIMARY KEY,
    token       VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.sessions (
    id              VARCHAR(255) PRIMARY KEY,
    user_id         BIGINT,
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    payload         TEXT NOT NULL,
    last_activity   INTEGER NOT NULL
);
CREATE INDEX resc_sessions_user_id_index ON rescate.sessions (user_id);
CREATE INDEX resc_sessions_last_activity_index ON rescate.sessions (last_activity);

CREATE TABLE rescate.cache (
    key         VARCHAR(255) PRIMARY KEY,
    value       TEXT NOT NULL,
    expiration  INTEGER NOT NULL
);

CREATE TABLE rescate.cache_locks (
    key         VARCHAR(255) PRIMARY KEY,
    owner       VARCHAR(255) NOT NULL,
    expiration  INTEGER NOT NULL
);

CREATE TABLE rescate.centers (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    direccion   VARCHAR(255),
    latitud     NUMERIC(12, 8),
    longitud    NUMERIC(12, 8),
    contacto    VARCHAR(255),
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.people (
    id                          BIGSERIAL PRIMARY KEY,
    usuario_id                  BIGINT REFERENCES core.usuarios (usuarioid) ON DELETE CASCADE,
    nombre                      VARCHAR(255) NOT NULL,
    ci                          VARCHAR(255) NOT NULL,
    telefono                    VARCHAR(255),
    es_cuidador                 BOOLEAN NOT NULL DEFAULT FALSE,
    created_at                  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at                  TIMESTAMP(0) WITHOUT TIME ZONE,
    foto_path                   VARCHAR(255),
    cuidador_center_id          BIGINT REFERENCES rescate.centers (id) ON DELETE SET NULL,
    cuidador_aprobado           BOOLEAN,
    cuidador_motivo_revision    TEXT
);
CREATE UNIQUE INDEX resc_people_ci_unique ON rescate.people (ci);

CREATE TABLE rescate.species (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.animal_statuses (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.animal_conditions (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    severidad   INTEGER NOT NULL DEFAULT 3,
    activo      BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE rescate.incident_types (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    riesgo      INTEGER NOT NULL DEFAULT 1,
    activo      BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE rescate.reports (
    id                      BIGSERIAL PRIMARY KEY,
    persona_id              BIGINT REFERENCES rescate.people (id) ON DELETE CASCADE,
    aprobado                BOOLEAN NOT NULL DEFAULT FALSE,
    imagen_url              VARCHAR(255),
    observaciones           VARCHAR(255),
    created_at              TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at              TIMESTAMP(0) WITHOUT TIME ZONE,
    latitud                 NUMERIC(12, 8),
    longitud                NUMERIC(12, 8),
    direccion               VARCHAR(255),
    condicion_inicial_id    BIGINT REFERENCES rescate.animal_conditions (id),
    tipo_incidente_id       BIGINT REFERENCES rescate.incident_types (id),
    tamano                  VARCHAR(255),
    puede_moverse           BOOLEAN,
    urgencia                INTEGER,
    incendio_id             INTEGER
);

CREATE TABLE rescate.animals (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255),
    sexo        VARCHAR(255) NOT NULL,
    descripcion TEXT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    reporte_id  BIGINT REFERENCES rescate.reports (id) ON DELETE CASCADE
);

CREATE TABLE rescate.animal_files (
    id              BIGSERIAL PRIMARY KEY,
    especie_id      BIGINT NOT NULL REFERENCES rescate.species (id) ON DELETE CASCADE,
    imagen_url      VARCHAR(255),
    estado_id       BIGINT NOT NULL REFERENCES rescate.animal_statuses (id) ON DELETE CASCADE,
    liberacion_id   BIGINT,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    animal_id       BIGINT REFERENCES rescate.animals (id) ON DELETE CASCADE,
    centro_id       BIGINT REFERENCES rescate.centers (id) ON DELETE SET NULL
);

CREATE TABLE rescate.releases (
    id              BIGSERIAL PRIMARY KEY,
    direccion       VARCHAR(255),
    detalle         TEXT,
    latitud         NUMERIC(12, 8),
    longitud        NUMERIC(12, 8),
    aprobada        BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    animal_file_id  BIGINT REFERENCES rescate.animal_files (id) ON DELETE CASCADE,
    imagen_url      VARCHAR(255),
    UNIQUE (animal_file_id)
);

ALTER TABLE rescate.animal_files
    ADD CONSTRAINT animal_files_liberacion_fk
    FOREIGN KEY (liberacion_id) REFERENCES rescate.releases (id) ON DELETE SET NULL;

CREATE TABLE rescate.animal_histories (
    id               BIGSERIAL PRIMARY KEY,
    animal_file_id   BIGINT REFERENCES rescate.animal_files (id) ON DELETE CASCADE,
    changed_at       TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado_anterior  TEXT NOT NULL,
    estado_nuevo     TEXT NOT NULL,
    observaciones    TEXT NOT NULL,
    old_values       TEXT,
    new_values       TEXT
);
CREATE INDEX resc_animal_hist_file_changed_idx ON rescate.animal_histories (animal_file_id, changed_at);

CREATE TABLE rescate.rescuers (
    id                  BIGSERIAL PRIMARY KEY,
    persona_id          BIGINT NOT NULL REFERENCES rescate.people (id) ON DELETE CASCADE,
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    cv_documentado      VARCHAR(255),
    aprobado            BOOLEAN,
    motivo_revision     TEXT,
    motivo_postulacion  TEXT
);

CREATE TABLE rescate.veterinarians (
    id                  BIGSERIAL PRIMARY KEY,
    especialidad        VARCHAR(255),
    persona_id          BIGINT NOT NULL REFERENCES rescate.people (id) ON DELETE CASCADE,
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    cv_documentado      VARCHAR(255),
    aprobado            BOOLEAN,
    motivo_revision     TEXT,
    motivo_postulacion  TEXT
);

CREATE TABLE rescate.treatment_types (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.medical_evaluations (
    id                  BIGSERIAL PRIMARY KEY,
    tratamiento_id     BIGINT NOT NULL REFERENCES rescate.treatment_types (id) ON DELETE CASCADE,
    descripcion         TEXT,
    fecha               DATE,
    veterinario_id      BIGINT NOT NULL REFERENCES rescate.veterinarians (id) ON DELETE CASCADE,
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    imagen_url          VARCHAR(255),
    animal_file_id      BIGINT REFERENCES rescate.animal_files (id) ON DELETE SET NULL,
    diagnostico         TEXT,
    peso                NUMERIC(10, 2),
    temperatura         NUMERIC(10, 2),
    tratamiento_texto  TEXT,
    recomendacion       VARCHAR(255),
    apto_traslado       VARCHAR(255)
);

CREATE TABLE rescate.care_types (
    id              BIGSERIAL PRIMARY KEY,
    nombre          VARCHAR(255) NOT NULL,
    descripcion     TEXT,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    es_alimentacion BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE rescate.cares (
    id                BIGSERIAL PRIMARY KEY,
    hoja_animal_id    BIGINT NOT NULL REFERENCES rescate.animal_files (id) ON DELETE CASCADE,
    tipo_cuidado_id   BIGINT NOT NULL REFERENCES rescate.care_types (id) ON DELETE CASCADE,
    descripcion       TEXT,
    fecha             DATE,
    created_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at        TIMESTAMP(0) WITHOUT TIME ZONE,
    imagen_url        VARCHAR(255)
);

CREATE TABLE rescate.feeding_types (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    descripcion TEXT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.feeding_frequencies (
    id          BIGSERIAL PRIMARY KEY,
    nombre      VARCHAR(255) NOT NULL,
    descripcion TEXT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.feeding_portions (
    id          BIGSERIAL PRIMARY KEY,
    cantidad    INTEGER NOT NULL,
    unidad      VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.care_feedings (
    id                      BIGSERIAL PRIMARY KEY,
    care_id                 BIGINT NOT NULL REFERENCES rescate.cares (id) ON DELETE CASCADE,
    feeding_type_id         BIGINT NOT NULL REFERENCES rescate.feeding_types (id) ON DELETE CASCADE,
    feeding_frequency_id    BIGINT NOT NULL REFERENCES rescate.feeding_frequencies (id) ON DELETE CASCADE,
    feeding_portion_id      BIGINT NOT NULL REFERENCES rescate.feeding_portions (id) ON DELETE CASCADE,
    created_at              TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at              TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.transfers (
    id                  BIGSERIAL PRIMARY KEY,
    persona_id          BIGINT NOT NULL REFERENCES rescate.people (id) ON DELETE CASCADE,
    centro_id           BIGINT NOT NULL REFERENCES rescate.centers (id) ON DELETE CASCADE,
    observaciones       VARCHAR(255),
    created_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at          TIMESTAMP(0) WITHOUT TIME ZONE,
    primer_traslado     BOOLEAN NOT NULL DEFAULT TRUE,
    animal_id           BIGINT REFERENCES rescate.animals (id) ON DELETE SET NULL,
    latitud             NUMERIC(12, 8),
    longitud            NUMERIC(12, 8),
    reporte_id          BIGINT REFERENCES rescate.reports (id) ON DELETE SET NULL
);

-- Roles y permisos Spatie: core (00_core_auth.sql)

CREATE TABLE rescate.personal_access_tokens (
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
CREATE INDEX resc_pat_tokenable_idx ON rescate.personal_access_tokens (tokenable_type, tokenable_id);
CREATE INDEX resc_pat_expires_at_idx ON rescate.personal_access_tokens (expires_at);

CREATE TABLE rescate.contact_messages (
    id          BIGSERIAL PRIMARY KEY,
    user_id     BIGINT NOT NULL REFERENCES core.usuarios (usuarioid) ON DELETE CASCADE,
    motivo      VARCHAR(255) NOT NULL,
    mensaje     TEXT NOT NULL,
    leido       BOOLEAN NOT NULL DEFAULT FALSE,
    leido_at    TIMESTAMP(0) WITHOUT TIME ZONE,
    leido_por   BIGINT,
    created_at  TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at  TIMESTAMP(0) WITHOUT TIME ZONE
);

CREATE TABLE rescate.user_tracking (
    id                      BIGSERIAL PRIMARY KEY,
    user_id                 BIGINT REFERENCES core.usuarios (usuarioid) ON DELETE SET NULL,
    performed_by            BIGINT,
    action_type             VARCHAR(255) NOT NULL,
    action_description      VARCHAR(255) NOT NULL,
    related_model_type      VARCHAR(255),
    related_model_id        BIGINT,
    valores_antiguos        TEXT,
    valores_nuevos          TEXT,
    metadata                TEXT,
    realizado_en            TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at              TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at              TIMESTAMP(0) WITHOUT TIME ZONE
);
CREATE INDEX resc_ut_user_realizado_idx ON rescate.user_tracking (user_id, realizado_en);
CREATE INDEX resc_ut_performed_idx ON rescate.user_tracking (performed_by, realizado_en);
CREATE INDEX resc_ut_action_type_idx ON rescate.user_tracking (action_type);
CREATE INDEX resc_ut_related_idx ON rescate.user_tracking (related_model_type, related_model_id);
CREATE INDEX resc_ut_realizado_idx ON rescate.user_tracking (realizado_en);
