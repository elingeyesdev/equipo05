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
