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

# Generar Key
php artisan key:generate --force

# Migraciones y Seeders
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

echo "🌱 Ejecutando Seeders..."
php artisan db:seed --force

# Storage Link
php artisan storage:link

echo "🚀 Iniciando PHP-FPM..."
exec php-fpm