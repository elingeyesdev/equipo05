-- Inicializacion unificada (primer arranque del volumen). Sin bash: evita CRLF en Windows.
-- Los .sql montados en /docker-entrypoint-sql deben estar en LF (ver .gitattributes).
\set ON_ERROR_STOP on

\echo '>> 01_extensions_and_schemas.sql'
\i /docker-entrypoint-sql/01_extensions_and_schemas.sql
\echo '>> 00_core_auth.sql (usuarios y roles centralizados)'
\i /docker-entrypoint-sql/00_core_auth.sql
\echo '>> 04_mod_inventario_transparencia.sql (esquema transparencia)'
\i /docker-entrypoint-sql/04_mod_inventario_transparencia.sql
\echo '>> 04a_inventario_schema_reset.sql'
\i /docker-entrypoint-sql/04a_inventario_schema_reset.sql
\echo '>> 02_mod_incendios.sql'
\i /docker-entrypoint-sql/02_mod_incendios.sql
\echo '>> 03_mod_rescate.sql'
\i /docker-entrypoint-sql/03_mod_rescate.sql
\echo '>> 06_mod_logistica.sql'
\i /docker-entrypoint-sql/06_mod_logistica.sql
\echo '>> 07_mod_seguimiento.sql'
\i /docker-entrypoint-sql/07_mod_seguimiento.sql
\echo '>> 08_mod_cuadrillas.sql'
\i /docker-entrypoint-sql/08_mod_cuadrillas.sql

\echo '>> init unificado: listo'
