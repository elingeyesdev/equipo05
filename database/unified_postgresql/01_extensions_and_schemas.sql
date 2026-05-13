-- =============================================================================
-- Conectado a la base: equipo05_unificado
-- =============================================================================

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS postgis;

CREATE SCHEMA IF NOT EXISTS inventario   AUTHORIZATION CURRENT_USER;
CREATE SCHEMA IF NOT EXISTS incendios   AUTHORIZATION CURRENT_USER;
CREATE SCHEMA IF NOT EXISTS rescate     AUTHORIZATION CURRENT_USER;
CREATE SCHEMA IF NOT EXISTS logistica   AUTHORIZATION CURRENT_USER;
CREATE SCHEMA IF NOT EXISTS seguimiento AUTHORIZATION CURRENT_USER;
CREATE SCHEMA IF NOT EXISTS cuadrillas  AUTHORIZATION CURRENT_USER;
CREATE SCHEMA IF NOT EXISTS core        AUTHORIZATION CURRENT_USER;

COMMENT ON SCHEMA inventario   IS 'Donacion recepcion inventario / transparencia';
COMMENT ON SCHEMA incendios    IS 'Monitoreo incendios simulacion';
COMMENT ON SCHEMA rescate      IS 'Rescate animales silvestres';
COMMENT ON SCHEMA logistica    IS 'Logistica transportacion donaciones';
COMMENT ON SCHEMA seguimiento  IS 'Seguimiento voluntarios comunarios';
COMMENT ON SCHEMA cuadrillas   IS 'Cuadrillas incendio kardex cursos';
COMMENT ON SCHEMA core         IS 'Reservado: identidad global / auditoria cruzada';

GRANT ALL ON SCHEMA inventario   TO CURRENT_USER;
GRANT ALL ON SCHEMA incendios    TO CURRENT_USER;
GRANT ALL ON SCHEMA rescate      TO CURRENT_USER;
GRANT ALL ON SCHEMA logistica    TO CURRENT_USER;
GRANT ALL ON SCHEMA seguimiento  TO CURRENT_USER;
GRANT ALL ON SCHEMA cuadrillas   TO CURRENT_USER;
GRANT ALL ON SCHEMA core         TO CURRENT_USER;
