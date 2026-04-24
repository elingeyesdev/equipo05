# Docker - Aplicación de Donaciones

## Requisitos Previos
- Docker instalado
- Docker Compose instalado
- PostgreSQL corriendo en el host (fuera de Docker)

## Configuración

### 1. Configurar Variables de Entorno

Copia `.env.example` a `.env` y configura:

```bash
DB_CONNECTION=pgsql
DB_HOST=host.docker.internal  # Para conectar a DB del host
DB_PORT=5432
DB_DATABASE=nombre_de_tu_bd
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 2. Construir y Levantar Contenedores

```bash
docker-compose up -d --build
```

### 3. Instalar Dependencias (primera vez)

```bash
docker-compose exec app composer install
```

### 4. Generar Key de Aplicación

```bash
docker-compose exec app php artisan key:generate
```

### 5. Ejecutar Migraciones

```bash
docker-compose exec app php artisan migrate
```

## Comandos Útiles

### Ver logs
```bash
docker-compose logs -f
```

### Acceder al contenedor de la app
```bash
docker-compose exec app bash
```

### Detener contenedores
```bash
docker-compose down
```

### Reconstruir contenedores
```bash
docker-compose up -d --build
```

### Ejecutar artisan commands
```bash
docker-compose exec app php artisan [comando]
```

## Acceso a la Aplicación

- **URL**: http://localhost:8080
- La app se conecta a tu base de datos PostgreSQL local

## Troubleshooting

### Error de conexión a base de datos
- Verifica que PostgreSQL esté corriendo en tu host
- Asegúrate de que PostgreSQL acepte conexiones desde Docker
- En tu `pg_hba.conf`, agrega: `host all all 172.0.0.0/8 md5`

### Permisos de archivos
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

## Despliegue con docker

Para comprobar la información del despliegue usando Docker, consulte el documento **Docker.md**.
