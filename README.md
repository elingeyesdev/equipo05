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
- Codigo fuente incorporado para proxima integracion modular:
  - `modulos/monitoreo-incendios-simulacion-main`
  - `modulos/rescate-animales-silvestres-main`
- Accesos iniciales agregados en el sistema principal:
  - Web: `/incendios` y `/rescate`
  - API activa de incendios: `/api/incendios/*`
  - API activa de rescate: `/api/rescate/*`
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
  - Suite de pruebas actual en verde (`php artisan test`: 5 pruebas, 0 fallos).

## Arranque local
1. Instalar dependencias:
   - `composer install`
   - `npm install`
2. Copiar variables:
   - `cp .env.example .env` (si aplica en tu entorno)
3. Generar clave:
   - `php artisan key:generate`
4. Ejecutar migraciones:
   - `php artisan migrate`
   - `php artisan migrate --database=inventario --path=modulos/donacion-recepcion-inventario-main/database/migrations`
5. Levantar entorno:
   - `php artisan serve`
   - `npm run dev`

## Punto de seguimiento de integracion
- Ruta: `/fusion/fase1`
- Pantalla de control de avance de la unificacion.

## Documento tecnico de fase
- Ver `docs/FASE1_UNIFICACION.md`
- Ver bitacora de avances en `main`: `docs/BITACORA_INTEGRACION_MAIN.md`
