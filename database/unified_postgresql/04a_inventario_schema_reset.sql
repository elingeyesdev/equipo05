-- Vacia el esquema inventario para aplicar migraciones Laravel del modulo almacen.
-- Ejecutar antes de: php artisan db:setup-inventario
-- (o lo hace ese comando automaticamente)

DROP SCHEMA IF EXISTS inventario CASCADE;
CREATE SCHEMA inventario AUTHORIZATION CURRENT_USER;
GRANT ALL ON SCHEMA inventario TO CURRENT_USER;
COMMENT ON SCHEMA inventario IS 'Donacion recepcion inventario / almacen (migraciones Laravel)';
