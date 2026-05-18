-- =============================================================================
-- Reinicia el esquema inventario para el modulo Laravel de almacen/donaciones
-- (donacion-recepcion-inventario-main). El archivo 04_mod_inventario_transparencia.sql
-- define otro modelo de datos (donacionid, campanias transparencia) incompatible.
--
-- Despues de este script ejecutar en el proyecto:
--   php artisan inventario:setup-database --seed
-- =============================================================================

SET search_path TO inventario, public;

DROP SCHEMA IF EXISTS inventario CASCADE;
CREATE SCHEMA inventario AUTHORIZATION CURRENT_USER;
GRANT ALL ON SCHEMA inventario TO CURRENT_USER;

SET search_path TO inventario, public;
