# Sistema de Gestion de Incendios - Equipo 05

Proyecto academico desarrollado con Laravel para apoyar la gestion y monitoreo de incidentes de incendios.

## Integrantes y ramas

- `karen` - Lider del equipo
- `santiago` - Modulos web de notificaciones e historial
- `didier` - Modulos asignados del sprint
- `main` - Rama de integracion

## Configuracion inicial

1. Clonar el repositorio:
   - `git clone https://github.com/elingeyesdev/equipo05.git`
2. Entrar al proyecto:
   - `cd equipo05`
3. Cambiar a tu rama:
   - `git checkout santiago` (o la rama correspondiente)
4. Configurar Laravel:
   - `composer install`
   - `cp .env.example .env`
   - `php artisan key:generate`
   - `php artisan migrate`
5. Levantar el servidor:
   - `php artisan serve`

## Modulos trabajados en la rama `santiago` (Sprint 0)

### 1) Modulo web de notificaciones de incendios

Permite visualizar y gestionar notificaciones generadas por eventos del sistema.

**Funciones implementadas**
- Listado de notificaciones ordenadas por no leidas y fecha.
- Visualizacion de tipo (`alerta`, `emergencia`, `info`) y mensaje.
- Marcado individual de notificacion como leida.
- Marcado masivo de todas las notificaciones como leidas.

**Rutas**
- `GET /notificaciones`
- `PATCH /notificaciones/{notificacion}/leida`
- `PATCH /notificaciones/leidas`

**Archivos principales**
- `app/Http/Controllers/NotificacionController.php`
- `resources/views/notificaciones/index.blade.php`

### 2) Modulo web de historial de incendios

Permite consultar los cambios historicos de estado de cada incendio.

**Funciones implementadas**
- Listado cronologico (descendente) de cambios registrados.
- Filtro por incendio.
- Filtro por estado nuevo.
- Visualizacion de estado anterior, estado nuevo, descripcion y fecha de cambio.

**Ruta**
- `GET /historial-incendios`

**Archivos principales**
- `app/Http/Controllers/HistorialIncendioController.php`
- `resources/views/historial/index.blade.php`

## Apoyo al flujo base del sistema

Para asegurar que el proyecto arranque y los modulos funcionen correctamente, en la rama `santiago` tambien se completo el flujo de gestion de incendios:

- Registro, edicion y eliminacion de incendios.
- Registro automatico de historial al crear y cambiar estado.
- Creacion automatica de notificaciones al crear incendio y al cambiar estado.

**Archivos relacionados**
- `app/Http/Controllers/IncendioController.php`
- `resources/views/monitoreo/create.blade.php`
- `resources/views/monitoreo/edit.blade.php`
- `resources/views/monitoreo/_form.blade.php`
- `routes/web.php`

## Flujo de trabajo Git recomendado

1. `git add .`
2. `git commit -m "Descripcion breve del avance"`
3. `git push origin santiago`
