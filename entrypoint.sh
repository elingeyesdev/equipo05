#!/bin/bash
set -e

# Copiar .env si no existe
if [ ! -f .env ]; then
    echo "📄 Creando .env desde .env.example..."
    cp .env.example .env
fi

# Instalar dependencias
echo "📦 Instalando dependencias..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos
echo "🔒 Asignando permisos..."
chmod -R 777 storage bootstrap/cache

# Generar APP_KEY solo si falta (evita error si .env no define APP_KEY)
if ! grep -q '^APP_KEY=.\+' .env 2>/dev/null; then
    php artisan key:generate --force
fi

# Migraciones/seeders: ejecutar manualmente según la guía (db:setup-unificado, RoleSeeder, etc.)
# El migrate automático fallaba en Docker si faltan los .sqlite de módulos legacy.

# Storage Link (ignorar si ya existe)
php artisan storage:link 2>/dev/null || true
php artisan rescate:ensure-schema 2>/dev/null || true
php artisan rescate:ensure-media --sync-db 2>/dev/null || true

echo "🚀 Iniciando PHP-FPM..."
exec php-fpm