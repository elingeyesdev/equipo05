# Sistema de Donaciones - Alas Chiquitanas

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

Sistema integral de gestión de donaciones desarrollado con Laravel 11 para Alas Chiquitanas. Este sistema permite la administración de donaciones, donantes, recolectores, campañas y solicitudes de ayuda.

## 📋 Tabla de Contenidos

- [Características Principales](#características-principales)
- [Requisitos Previos](#requisitos-previos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Uso](#uso)
- [Integración con API Gateway](#integración-con-api-gateway)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Licencia](#licencia)

## ✨ Características Principales

- **Gestión de Usuarios**: Sistema completo de usuarios con roles y permisos (usando Spatie)
- **Gestión de Donantes**: Registro y administración de personas y empresas donantes
- **Gestión de Donaciones**: Control de donaciones monetarias y en especie
- **Campañas**: Creación y seguimiento de campañas de donación
- **Recolectores**: Gestión de personal de recolección con licencias de conducir
- **Reportes**: Generación de reportes y estadísticas
- **API Gateway Integration**: Integración con sistema de gateway para búsqueda de datos
- **Autocompletado Inteligente**: Búsqueda automática de datos de usuarios por CI

## 🔧 Requisitos Previos

Antes de instalar el proyecto, asegúrate de tener instalado lo siguiente:

### Software Requerido

1. **PHP >= 8.2**
   - Extensiones requeridas:
     - OpenSSL
     - PDO
     - Mbstring
     - Tokenizer
     - XML
     - Ctype
     - JSON
     - BCMath
     - Fileinfo
     - pgsql (para PostgreSQL)

2. **Composer** (Gestor de dependencias de PHP)
   - Descargar desde: [getcomposer.org](https://getcomposer.org/)

3. **PostgreSQL >= 14**
   - Descargar desde: [postgresql.org](https://www.postgresql.org/download/)

4. **Node.js >= 18** y **npm** (para compilar assets)
   - Descargar desde: [nodejs.org](https://nodejs.org/)

5. **Git** (opcional, para clonar el repositorio)
   - Descargar desde: [git-scm.com](https://git-scm.com/)

### Verificar Instalaciones

```bash
# Verificar versión de PHP
php -v

# Verificar versión de Composer
composer --version

# Verificar versión de PostgreSQL
psql --version

# Verificar versión de Node.js y npm
node -v
npm -v
```

## 📦 Instalación

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd AlasPHP
```

O si descargaste el proyecto como ZIP, extráelo y navega a la carpeta:

```bash
cd ruta/a/AlasPHP
```

### 2. Instalar Dependencias de PHP

```bash
composer install
```

Si encuentras errores, intenta:

```bash
composer install --ignore-platform-reqs
```

### 3. Instalar Dependencias de Node.js

```bash
npm install
```

### 4. Configurar Variables de Entorno

Copia el archivo de ejemplo y edítalo con tus configuraciones:

```bash
# En Windows (PowerShell)
Copy-Item .env.example .env

# En Linux/Mac
cp .env.example .env
```

Edita el archivo `.env` con tus datos:

```env
APP_NAME="Sistema de Donaciones"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=donaciones_php3
DB_USERNAME=postgres
DB_PASSWORD=tu_password_aqui

# URL del API Gateway (para búsqueda de usuarios)
API_BASE_URL_ADS=http://gatealas.dasalas.shop

# Configuración de correo
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_password_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 6. Crear la Base de Datos

Conéctate a PostgreSQL y crea la base de datos:

```bash
# Acceder a PostgreSQL
psql -U postgres

# Crear la base de datos
CREATE DATABASE donaciones_php3;

# Salir
\q
```

### 7. Ejecutar Migraciones

```bash
php artisan migrate
```

### 8. Ejecutar Seeders (Datos Iniciales)

```bash
php artisan db:seed
```

Esto creará:
- Roles y permisos iniciales
- Usuario administrador por defecto
- Datos de ejemplo (opcional)

### 9. Crear Enlaces Simbólicos para Storage

```bash
php artisan storage:link
```

### 10. Compilar Assets

Para desarrollo:
```bash
npm run dev
```

Para producción:
```bash
npm run build
```

## 🚀 Uso

### Iniciar el Servidor de Desarrollo

```bash
php artisan serve
```

El servidor estará disponible en: `http://localhost:8000`

### Iniciar Vite (para desarrollo con hot-reload)

En otra terminal:

```bash
npm run dev
```

### Acceder al Sistema

1. Abre tu navegador en `http://localhost:8000`
2. Usa las credenciales del usuario administrador creado en los seeders:
   - **Email**: admin123456@gmail.com (verifica en los seeders)
   - **Password**: admin123456 (verifica en los seeders)

## ⚙️ Configuración

### Limpiar Caché

Si experimentas problemas, limpia el caché:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

O usa el comando todo-en-uno:

```bash
php artisan optimize:clear
```



## 📁 Estructura del Proyecto

```
AlasPHP/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Controladores del sistema
│   │   └── Requests/       # Request validation
│   ├── Models/             # Modelos Eloquent
│   └── Providers/          # Service Providers
├── config/                 # Archivos de configuración
├── database/
│   ├── migrations/         # Migraciones de base de datos
│   └── seeders/           # Seeders (datos iniciales)
├── public/                 # Punto de entrada público
├── resources/
│   ├── views/             # Vistas Blade
│   └── js/                # JavaScript/Vue components
├── routes/
│   ├── web.php            # Rutas web
│   └── api.php            # Rutas API
├── storage/               # Archivos generados
└── tests/                 # Tests automatizados
```

## 🔌 Integración con API Gateway

El sistema se integra con un API Gateway para búsqueda automática de datos de usuarios:

### Endpoint de Búsqueda

```
GET /api/gateway/registro/ci/{ci}
```

### Configuración

En el archivo `.env`:

```env
API_BASE_URL_ADS=http://gatealas.dasalas.shop
```

### Funcionamiento

Cuando un usuario ingresa un CI en el formulario de registro:
1. El sistema hace una petición al gateway
2. Si encuentra datos, autocompletará los campos: nombres, apellidos y teléfono
3. Muestra un mensaje indicando de qué sistema provienen los datos

## 🛠️ Tecnologías Utilizadas

- **Framework**: Laravel 11
- **Frontend**: Blade, Bootstrap, AdminLTE
- **Base de Datos**: PostgreSQL
- **Autenticación**: Laravel Breeze/Sanctum
- **Roles y Permisos**: Spatie Laravel Permission
- **Build Tool**: Vite
- **Gestión de Dependencias**: Composer, npm

## 📝 Comandos Útiles

### Artisan Commands

```bash
# Crear un nuevo controlador
php artisan make:controller NombreController

# Crear un nuevo modelo con migración
php artisan make:model NombreModelo -m

# Crear un nuevo request
php artisan make:request NombreRequest

# Ver todas las rutas
php artisan route:list

# Ejecutar tests
php artisan test
```

### Base de Datos

```bash
# Refrescar la base de datos (elimina todos los datos)
php artisan migrate:fresh

# Refrescar con seeders
php artisan migrate:fresh --seed

# Crear un nuevo seeder
php artisan make:seeder NombreSeeder
```

## 🐛 Solución de Problemas

### Error de permisos en storage/

```bash
# Windows (como administrador)
icacls storage /grant Users:F /T
icacls bootstrap/cache /grant Users:F /T

# Linux/Mac
chmod -R 775 storage bootstrap/cache
```

### Error de conexión a base de datos

1. Verifica que PostgreSQL esté corriendo
2. Verifica las credenciales en `.env`
3. Asegúrate de que la base de datos existe

### El CSS/JS no se carga

```bash
npm run build
php artisan optimize:clear
```

## 📧 Contacto y Soporte

Para reportar problemas o solicitar ayuda:
- Crea un issue en el repositorio
- Contacta al equipo de desarrollo

Desarrollado con ❤️ para Alas Chiquitanas
