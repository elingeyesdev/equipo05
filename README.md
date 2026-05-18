# Integracion Donaciones - Fase 1

Proyecto nuevo para unificar de forma gradual:

- `transparencia_donaciones_voluntarios-main` (base visual y flujo actual).
- `donacion-recepcion-inventario-main` (modulo de recepcion e inventario).

## Base de esta fase
- Frontend: se mantiene el diseno del proyecto de Transparencia.
- Backend: Laravel monolitico modular.
- Base de datos temporal: SQLite dual.
  - Transparencia: `database/database.sqlite`
  - Inventario: `database/inventario.sqlite`

## Modulos integrados en esta fase
- Modulo Transparencia (rutas base existentes).
- Modulo Donacion/Recepcion/Inventario integrado bajo prefijos:
  - Web: `/inventario/*`
  - API: `/api/inventario/*`
- Modulo Seguimiento Voluntarios Comunitarios integrado bajo prefijos:
  - Web: `/seguimiento/modulo/*`
  - API: `/api/seguimiento/*`
- Modulo Cuadrillas Incendios Kardex Cursos integrado bajo prefijos:
  - Web: `/cuadrillas/modulo/*`
  - API: `/api/cuadrillas/*`
- Codigo fuente incorporado para proxima integracion modular:
  - `modulos/monitoreo-incendios-simulacion-main`
  - `modulos/rescate-animales-silvestres-main`
- Accesos iniciales agregados en el sistema principal:
  - Web: `/incendios` y `/rescate`
  - API activa de incendios: `/api/incendios/*`
  - API activa de rescate: `/api/rescate/*`
  - Web de seguimiento: `/seguimiento`
  - API activa de seguimiento: `/api/seguimiento/*`
  - Web de cuadrillas: `/cuadrillas`
  - API activa de cuadrillas: `/api/cuadrillas/*`
- Estado de estabilidad actual:
  - El enrutamiento integrado de los tres sistemas se registra correctamente (`php artisan route:list`).
  - Se aplicaron correcciones de compatibilidad en servicios y rutas del modulo rescate para evitar errores fatales de arranque.
  - Migraciones de `rescate` adaptadas para SQLite local del monolito y ejecutadas exitosamente.
  - Las entradas de sidebar `Monitoreo de Incendios` y `Rescate de Animales Silvestres` apuntan a vistas integradas estables (`/incendios` y `/rescate`) con autenticacion central.
  - Las rutas internas completas de los modulos quedaron bajo prefijos sin colision con el sidebar:
    - Incendios: `/incendios/modulo/*`
    - Rescate: `/rescate/modulo/*`
  - Se activaron middlewares de conexion dedicada por modulo para asegurar consultas sobre SQLite correcto:
    - `incendios.db`
    - `rescate.db`
  - Se valido navegacion autenticada de rutas internas clave:
    - `/incendios/modulo`
    - `/incendios/modulo/biomasas`
    - `/incendios/modulo/simulaciones`
    - `/rescate/modulo/home`
    - `/rescate/modulo/animals`
  - Se corrigieron rutas avanzadas de reportes y catalogos que estaban fallando:
    - `/incendios/modulo/reports/fires`
    - `/incendios/modulo/focos-incendios`
    - `/incendios/modulo/reports/biomasas`
    - `/incendios/modulo/reports/simulations`
    - `/rescate/modulo/centers`
    - `/rescate/modulo/species`
    - `/rescate/modulo/reports`
    - `/rescate/modulo/reports/claim`
  - Se endurecieron rutas CRUD para manejo de IDs inexistentes:
    - acciones `show/edit/destroy` ya no provocan `500`, ahora retornan `404` controlado.
  - Se corrigieron validaciones `exists` para usar la conexion SQLite de cada modulo:
    - Incendios: `exists:incendios.tipo_biomasa,id`
    - Rescate: `exists:rescate.*`
  - Se normalizaron redirecciones internas de controladores de Incendios a rutas `incendios.*` para evitar fallos tras crear/editar/eliminar.
  - Se agrega sincronizacion automatica del usuario autenticado hacia las tablas `users` de `incendios` y `rescate` para evitar errores de FK entre modulo y core.
  - Se agrega provision automatica de `people` en Rescate para que flujos autenticados de reportes/animales no fallen por falta de persona asociada.
  - Se corrigen validaciones transaccionales de `animal-records` para que `exists` consulte `rescate.*` y no la BD principal.
  - Se corrigen rutas dinamicas con parametros (`show/edit`) para evitar `500` en recursos inexistentes y metodos faltantes de controladores transaccionales.
  - Verificacion funcional real en servidor local:
    - Creacion de biomasa en `/incendios/modulo/biomasas` exitosa (insercion en `incendios.sqlite`).
    - Creacion de centro en `/rescate/modulo/centers` exitosa.
    - Creacion de animal en `/rescate/modulo/animals` exitosa (con reporte aprobado).
    - Edicion/eliminacion E2E validadas en biomasa, focos-incendios, centers y animals.
    - Aprobacion de reportes en Rescate corregida y operativa en SQLite (`/rescate/modulo/reports/{id}/approve`).
    - Barrido completo de rutas estaticas de ambos modulos (`106` endpoints GET sin parametros) sin errores `500/419`.
  - Suite de pruebas actual en verde (`php artisan test`: 5 pruebas, 0 fallos).
  - Modulo `Logistica Transportacion Donaciones` integrado en sidebar y rutas internas bajo:
    - `/logistica`
    - `/logistica/modulo/*`
  - Modulo `Seguimiento Voluntarios Comunitarios` integrado en sidebar y rutas internas bajo:
    - `/seguimiento`
    - `/seguimiento/modulo/*`
  - Modulo `Cuadrillas Incendios Kardex Cursos` integrado en sidebar y rutas internas bajo:
    - `/cuadrillas`
    - `/cuadrillas/modulo/*`
  - Pantalla de inicio de sesion del proyecto base ajustada para replicar la entrada publica de `@web`:
    - Boton `Solicitar ayuda` (acceso a `logistica.solicitud.create`).
    - Boton `Galeria de paquetes entregados` (acceso a `logistica.galeria`).

## PostgreSQL unificado (recomendado con Docker)

Un solo servidor PostgreSQL (`equipo05_unificado`) con un esquema por módulo. **Login único** en `core.usuarios` (ya no hay tablas `users` duplicadas por módulo).

Guías:
- `database/docker/CONEXION_DBEAVER_Y_LARAVEL.txt` — DBeaver, `.env`, credenciales de prueba
- `database/docker/ARRANQUE_SEGURO.txt` — checklist antes de `php artisan serve`

Arranque rápido:

```bash
docker compose up -d db_unificado
php artisan config:clear
php artisan db:setup-transparencia
php artisan db:seed --force
php artisan db:setup-inventario --fresh   # una vez
php artisan serve
```

Login de prueba: `admin123@gmail.com` / `admin123`

Si migras una base Docker antigua con tablas `users` repetidas:

```bash
php artisan db:consolidate-users-core --fresh-core
php artisan db:seed --force
```

## Arranque local (SQLite, sin Docker PG)
1. Instalar dependencias:
   - `composer install`
   - `npm install`
2. Copiar variables:
   - `cp .env.example .env` y usa `DATABASE_UNIFIED_POSTGRES=false`, `DB_CONNECTION=sqlite`
3. Generar clave:
   - `php artisan key:generate`
4. Ejecutar migraciones:
   - `php artisan migrate`
   - `php artisan migrate --database=inventario --path=modulos/donacion-recepcion-inventario-main/database/migrations`
  - `php artisan migrate --database=logistica --path=modulos/logistica-transportacion-donaciones-main/database/migrations`
5. Levantar entorno:
   - `php artisan serve`
   - `npm run dev`

## Acceso rapido previo al login
- En la pantalla de login del proyecto base se muestran botones publicos para flujo logistico:
  - `Solicitar ayuda`
  - `Galeria de paquetes entregados`
- Tambien se agregaron accesos publicos para modulos nuevos integrados:
  - `publico.cuadrillas.reporte` (`/publico/cuadrillas/reporte`)
  - `publico.cuadrillas.mapa` (`/publico/cuadrillas/mapa`)
  - `publico.seguimiento.info` (`/publico/seguimiento/info`)
- Estos accesos permiten iniciar el flujo de logistica antes de autenticarse, manteniendo el mismo estilo visual del modulo `@web`.

## Punto de seguimiento de integracion
- Ruta: `/fusion/fase1`
- Pantalla de control de avance de la unificacion.

## Documento tecnico de fase
- Ver `docs/FASE1_UNIFICACION.md`
- Ver bitacora de avances en `main`: `docs/BITACORA_INTEGRACION_MAIN.md`
