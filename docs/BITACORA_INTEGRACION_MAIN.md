# Bitacora de Integracion en main

Este documento registra, por hitos, los cambios aplicados directamente en la rama `main` del repositorio `elingeyesdev/equipo05`.

## Objetivo de integracion

Integrar completamente:

- `Monitoreo de Incendios`
- `Rescate de Animales Silvestres`

dentro de `equipo05`, con login unico y navegacion desde el menu lateral.

## Hitos

### Hito 1 - Copia completa de modulos fuente

Fecha: 2026-04-26

Cambios:

- Se incorpora el codigo fuente del sistema de monitoreo en:
  - `modulos/monitoreo-incendios-simulacion-main/`
- Se incorpora el codigo fuente del sistema de rescate en:
  - `modulos/rescate-animales-silvestres-main/`
- Se excluyen artefactos no versionables:
  - `vendor/`
  - `node_modules/`
  - archivos `.env`
  - logs runtime
- Se eliminaron archivos con credenciales embebidas para cumplir reglas de seguridad del repositorio:
  - `.env.example` y `.env.docker` del modulo de monitoreo
  - `docker-compose.yml` del modulo de monitoreo
  - `.env.example`, `SECRETS.md` y `CONFIGURACION_GMAIL.md` del modulo de rescate

Resultado esperado:

- Ambos sistemas quedan versionados dentro del proyecto principal para iniciar su adaptacion modular (rutas, auth unica, conexiones de BD y menu unificado).

### Hito 2 - Enrutamiento base y navegacion inicial

Fecha: 2026-04-26

Cambios:

- Se agregan conexiones SQLite dedicadas en configuracion:
  - `incendios` -> `database/incendios.sqlite`
  - `rescate` -> `database/rescate.sqlite`
- Se agregan variables de entorno en `.env.example`:
  - `INCENDIOS_DB_DATABASE`
  - `RESCATE_DB_DATABASE`
- Se habilitan rutas web protegidas en el sistema principal:
  - `/incendios`
  - `/rescate`
- Se habilitan endpoints API de estado:
  - `/api/incendios/status`
  - `/api/rescate/status`
- Se agrega navegacion en el menu lateral:
  - `Monitoreo de Incendios`
  - `Rescate de Animales Silvestres`

Resultado esperado:

- Los dos nuevos sistemas ya aparecen dentro del sistema principal y son navegables desde el panel, dejando lista la siguiente etapa de adaptacion de controladores/modelos para login unico completo.

### Hito 3 - Integracion tecnica real del modulo Monitoreo de Incendios

Fecha: 2026-04-26

Cambios:

- Se migra el namespace del modulo de incendios a `Modules\\Incendios\\*`.
- Se registra autoload PSR-4 para incendios en `composer.json`.
- Se montan rutas web reales del modulo con prefijo:
  - `/incendios/*` y nombres `incendios.*`
- Se montan rutas API reales del modulo con prefijo:
  - `/api/incendios/*`
- Se desactiva `Auth::routes()` interno del modulo para respetar autenticacion centralizada.
- El menu lateral apunta ahora a `incendios.dashboard`.

Resultado esperado:

- El modulo de incendios deja de estar en modo placeholder y pasa a un montaje real dentro del sistema principal, con base tecnica lista para ajuste de login unico por entidad de usuario.

### Hito 4 - Integracion tecnica real del modulo Rescate de Animales Silvestres

Fecha: 2026-04-26

Cambios:

- Se migra el namespace del modulo de rescate a `Modules\\Rescate\\*`.
- Se registra autoload PSR-4 para rescate en `composer.json`.
- Se montan rutas web reales del modulo con prefijo:
  - `/rescate/*` y nombres `rescate.*`
- Se montan rutas API reales del modulo con prefijo:
  - `/api/rescate/*`
- Se desactiva `Auth::routes()` interno del modulo para respetar autenticacion centralizada.
- El menu lateral apunta ahora a `rescate.home`.

Resultado esperado:

- El modulo de rescate queda montado estructuralmente dentro del sistema principal, compartiendo el login central y preparado para normalizacion de modelo de usuario/roles.

### Hito 5 - Estabilizacion de arranque y rutas integradas

Fecha: 2026-04-26

Cambios:

- Se corrige null-safety en `OpenMeteoService` de rescate para evitar fallo por configuracion incompleta de `sipi_weather.api_url`.
- Se corrigen referencias de rutas en rescate que apuntaban por error a controladores de incendios.
- Se eliminan rutas de rescate que referenciaban controladores inexistentes (`AnimalProfileController`, `DispositionController`, `HealthRecordController`) para recuperar consistencia del enrutador.
- Se confirma registro completo de rutas con `php artisan route:list` sin excepciones fatales.

Resultado esperado:

- La aplicacion vuelve a arrancar y registrar rutas de los tres sistemas sin romper por clases faltantes o configuraciones nulas.

### Hito 6 - Compatibilidad SQLite completa para modulo Rescate

Fecha: 2026-04-26

Cambios:

- Se ajustan migraciones de rescate para que soporten SQLite (entorno local del proyecto principal) sin SQL exclusivo de PostgreSQL.
- Se agregan defensas por driver (`isSqlite`) en migraciones con:
  - `UPDATE ... FROM ...`
  - `ALTER TABLE ONLY ... ALTER COLUMN ...`
  - `DROP CONSTRAINT` y `DROP COLUMN` no compatibles en SQLite.
- Se completa ejecucion de migraciones de `rescate.sqlite` sin errores.
- Se amplian middlewares de rol en rutas de modulos para compatibilidad con roles actuales del sistema principal.

Verificacion ejecutada:

- `php artisan route:list` exitoso.
- `php artisan migrate --database=rescate --path=modulos/rescate-animales-silvestres-main/database/migrations` exitoso.
- Smoke test HTTP:
  - `/login` responde 200.
  - `/incendios` y `/rescate` redirigen correctamente a login cuando no hay sesion (302).

### Hito 7 - Validacion automatizada minima post-integracion

Fecha: 2026-04-26

Cambios:

- Se ajustan pruebas feature para reflejar el comportamiento real del sistema integrado:
  - `/` redirige a `/login` para usuario no autenticado.
  - `/login` carga correctamente.
  - `/incendios` y `/rescate` exigen autenticacion (redirect a `/login`).
- Se ejecuta la suite de pruebas de Laravel:
  - `5` pruebas aprobadas.
  - `0` fallos.

Resultado esperado:

- Existe una verificacion automatizada basica que cubre el flujo de acceso principal y evita regresiones evidentes tras la integracion modular.

### Hito 8 - Estabilizacion de accesos de sidebar para validacion funcional

Fecha: 2026-04-26

Cambios:

- Se normaliza el acceso principal de ambos modulos desde el sistema central:
  - `/incendios` -> `fusion.modulos.incendios`
  - `/rescate` -> `fusion.modulos.rescate`
- Se actualiza el sidebar para usar esas rutas estables como puntos de entrada unificados.
- Se agrega hardening en `DashboardController` de incendios para tolerar falta de datos del modulo durante pruebas de integracion (sin romper el panel principal).
- Se corrige redireccion raiz en rutas de rescate para usar nombres de ruta prefijados (`rescate.home` y `rescate.landing`).

Verificacion ejecutada:

- `php artisan route:list` exitoso.
- `php artisan test` exitoso (`5` pruebas aprobadas, `0` fallos).

Resultado esperado:

- El docente puede navegar los dos modulos desde el menu lateral dentro del sistema principal, con autenticacion unica y sin romper el flujo base del proyecto.

### Hito 9 - Correccion de errores internos en ejecucion real (VS Code local)

Fecha: 2026-04-26

Cambios:

- Se corrige colision de rutas entre accesos de sidebar y rutas internas de modulos:
  - Se mantiene el acceso estable del menu en:
    - `/incendios`
    - `/rescate`
  - Se mueven rutas internas completas de modulos a:
    - `/incendios/modulo/*`
    - `/rescate/modulo/*`
- Se ajustan enlaces del sidebar para navegar por URL estable (`/incendios`, `/rescate`) evitando error por resolucion de nombre de ruta en entornos con estado parcial.

Verificacion ejecutada:

- Smoke test autenticado en servidor local (`127.0.0.1:8000`):
  - `/dashboard` => `200`
  - `/incendios` => `200`
  - `/rescate` => `200`
- `php artisan test` exitoso (`5` pruebas aprobadas, `0` fallos).

Resultado esperado:

- El sistema deja de mostrar "Internal Server Error" al iniciar sesion y al abrir los accesos del sidebar para Incendios y Rescate en el proyecto `equipo05-main-integration`.

### Hito 10 - Activacion funcional de rutas internas de Incendios y Rescate

Fecha: 2026-04-26

Cambios:

- Se agregan middlewares dedicados para conexion de base de datos por modulo:
  - `UseIncendiosConnection` (`incendios.db`)
  - `UseRescateConnection` (`rescate.db`)
- Se aplican esos middlewares a los grupos:
  - `/incendios/modulo/*`
  - `/rescate/modulo/*`
- Se corrige compatibilidad de vistas para Incendios con componentes Blade `x-adminlte-*` mediante componentes anonimos locales en `resources/views/components/`.
- Se corrigen helpers `route()` en vistas de modulos para respetar prefijos de nombres (`incendios.*` y `rescate.animals.*`) en el entorno integrado.
- Se elimina recursion de layouts al evitar prepend de vistas del modulo dentro de middleware.

Verificacion ejecutada:

- Smoke test autenticado (`127.0.0.1:8000`):
  - `/incendios/modulo` => `200`
  - `/incendios/modulo/biomasas` => `200`
  - `/incendios/modulo/simulaciones` => `200`
  - `/rescate/modulo/home` => `200`
  - `/rescate/modulo/animals` => `200`
- `php artisan test` exitoso (`5` pruebas aprobadas, `0` fallos).

Resultado esperado:

- Los dos sistemas integrados ya exponen rutas internas funcionales dentro del monolito principal, mas alla del acceso de sidebar.

### Hito 11 - Correccion de reportes avanzados y permisos cruzados

Fecha: 2026-04-26

Cambios:

- Se corrigen errores 500 en Incendios (reportes y focos):
  - `isAdministrador()` reemplazado por validacion robusta compatible con `Usuario` del core.
  - Se agrega plantilla faltante `resources/views/vendor/pagination/no-prev-next.blade.php`.
- Se estabiliza Rescate para entorno de autenticacion unificada:
  - Los modelos de `Modules\\Rescate\\Models\\*` y `Modules\\Incendios\\Models\\*` se fijan a su conexion dedicada (`rescate` e `incendios`) sin alterar la conexion global.
  - Los middlewares `incendios.db` y `rescate.db` dejan de cambiar `database.default`, evitando conflictos con permisos/roles de Spatie en el core.
  - Se amplian middlewares de rol en `ReportController` para aceptar roles del sistema principal.
  - Se normalizan rutas `route()` en vistas/controladores de rescate al prefijo `rescate.*` y se corrigen referencias a login central.

Verificacion ejecutada:

- Smoke test autenticado (`127.0.0.1:8000`):
  - `/incendios/modulo/reports/fires` => `200`
  - `/incendios/modulo/focos-incendios` => `200`
  - `/incendios/modulo/reports/biomasas` => `200`
  - `/incendios/modulo/reports/simulations` => `200`
  - `/rescate/modulo/centers` => `200`
  - `/rescate/modulo/species` => `200`
  - `/rescate/modulo/reports` => `200`
  - `/rescate/modulo/reports/claim` => `200`
- `php artisan test` exitoso (`5` pruebas aprobadas, `0` fallos).

Resultado esperado:

- Se eliminan los errores internos de rutas avanzadas y queda operativa la navegacion de modulos integrados con autenticacion central.
