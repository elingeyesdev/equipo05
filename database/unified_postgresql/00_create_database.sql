-- =============================================================================
-- Ejecutar UNA SOLA VEZ conectado a la base "postgres" (administracion).
-- Luego en DBeaver: nueva conexion a la base "equipo05_unificado" y ejecutar
-- 01_extensions_and_schemas.sql y los demas en el orden de 00_INSTRUCCIONES.txt
-- =============================================================================

CREATE DATABASE equipo05_unificado
    WITH
    OWNER      = CURRENT_USER
    ENCODING   = 'UTF8'
    TEMPLATE   = template0;
