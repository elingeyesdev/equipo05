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
