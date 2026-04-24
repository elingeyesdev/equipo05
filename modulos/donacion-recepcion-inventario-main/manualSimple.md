# 📘 Manual de Instalación y Ejecución (Para que todo funcione a la primera)

Este manual está diseñado para que puedas levantar el proyecto **AlasPHP** sin dolores de cabeza. Sigue los pasos **exactamente** como están aquí.

---

## 🛠️ 1. Requisitos Previos

Antes de hacer nada, asegúrate de tener instalado:

1.  **Docker Desktop**: [Descargar aquí](https://www.docker.com/products/docker-desktop/).
    *   *Importante*: Asegúrate de que Docker esté **abierto y corriendo** (debes ver la ballenita en la barra de tareas).
2.  **Git**: [Descargar aquí](https://git-scm.com/downloads).

---

## 🚀 2. Primeros Pasos

### A. Clonar el repositorio
Si aún no tienes el código, clónalo en tu carpeta de preferencia:

```bash
git clone <URL_DEL_REPOSITORIO>
cd AlasPHP
```

### B. Configurar el entorno (.env)
1.  Busca el archivo `.env.example`.
2.  Cópialo y renómbralo a `.env`.
    *   En Windows (PowerShell): `cp .env.example .env`
    *   O simplemente copiar y pegar en el explorador de archivos y cambiarle el nombre.

---

## 🐳 3. Levantar el Proyecto con Docker

Este es el paso mágico. No necesitas instalar PHP ni Composer en tu Windows, Docker lo hace todo.

Abre tu terminal (PowerShell o CMD) en la carpeta del proyecto y ejecuta:

```bash
docker-compose up -d --build
```

*   `up`: Levanta los contenedores.
*   `-d`: Lo hace en segundo plano (para que no te bloquee la terminal).
*   `--build`: Reconstruye las imágenes para asegurar que tengas lo último.

**⏳ Espera unos minutos.** La primera vez tardará un poco descargando todo.

---

## 📦 4. Instalar Dependencias y Configurar Laravel

Una vez que Docker terminó (cuando te devuelve el control de la terminal), ejecuta estos comandos **uno por uno**:

### 1. Instalar librerías de PHP (Composer)
```bash
docker-compose exec app composer install
```

### 2. Generar la llave de la aplicación
```bash
docker-compose exec app php artisan key:generate
```

### 3. Correr las migraciones (Base de Datos)
Esto crea las tablas en la base de datos automáticamente.
```bash
docker-compose exec app php artisan migrate
```
*   *Nota*: Si dice "Nothing to migrate", ¡es buena señal! Significa que ya estás al día.

---

## 🌐 5. ¡A Probar!

Abre tu navegador y ve a:

👉 **http://localhost:8080**

¡Deberías ver la aplicación funcionando!

---

## 💡 Comandos Útiles (Cheat Sheet)

Guarda estos comandos, los usarás mucho:

| Acción | Comando |
| :--- | :--- |
| **Detener todo** | `docker-compose down` |
| **Iniciar todo** | `docker-compose up -d` |
| **Ver estado** | `docker-compose ps` |
| **Correr migraciones** | `docker-compose exec app php artisan migrate` |
| **Limpiar caché** | `docker-compose exec app php artisan optimize:clear` |
| **Entrar a la terminal del contenedor** | `docker-compose exec app bash` |

---

## ⚠️ Solución de Problemas Comunes

**1. Error: "Bind for 0.0.0.0:8080 failed: port is already allocated"**
*   **Causa**: Otro programa está usando el puerto 8080.
*   **Solución**: Cambia el puerto en `docker-compose.yml` (donde dice `8080:80` cámbialo a `8081:80`) y vuelve a correr `docker-compose up -d`. Luego entra a `localhost:8081`.

**2. Error de permisos en carpetas (storage/logs...)**
*   Ejecuta: `docker-compose exec app chmod -R 777 storage bootstrap/cache`

**3. La página se ve blanca o con error 500**
*   Asegúrate de haber corrido `composer install` y `key:generate`.
*   Revisa los logs: `docker-compose logs -f app`

---

¡Listo! Si sigues esto, no deberías tener problemas. 🚀
