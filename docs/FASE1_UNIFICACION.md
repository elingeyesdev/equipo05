# Fase 1 - Unificacion de Transparencia + Recepcion/Inventario

## Objetivo
Construir una base unica en esta carpeta para integrar, por etapas, los proyectos:

- `transparencia_donaciones_voluntarios-main` (base visual y flujo inicial).
- `donacion-recepcion-inventario-main` (modulo de inventario y operaciones fisicas).

## Lineamientos de esta fase
- No modificar los proyectos originales.
- Mantener frontend estilo Transparencia (layouts, componentes, navegacion).
- Usar SQLite de forma temporal (`database/database.sqlite`).
- Dejar preparada la estructura para migrar luego a PostgreSQL sin romper dominio.

## Estado actual
1. Proyecto base clonado desde Transparencia.
2. Configuracion local alineada a SQLite.
3. Pantalla de control de integracion: `fusion/fase1`.
4. Modulo inventario integrado con namespace propio `Modules\\Inventario`.
5. Rutas de inventario activas:
   - Web: `/inventario/*`
   - API: `/api/inventario/*`
6. Base SQLite separada para inventario: `database/inventario.sqlite`.

## Estrategia tecnica de integracion

### Paso 1: Unificacion de autenticacion y roles
- Mantener `users/roles/permissions` de la base actual.
- Mapear roles de Inventario al esquema Spatie existente.

### Paso 2: Integrar catalogos de inventario
- Incorporar tablas catalogo (productos, categorias, tallas, genero_ropa).
- Homologar nombres y claves para evitar duplicidad con tablas existentes.

### Paso 3: Integrar flujo de donaciones en especie
- Migrar logica de:
  - recepcion de donaciones,
  - detalle de donacion,
  - ubicacion en almacen/estante/espacio.
- Mantener trazabilidad de usuario/accion.

### Paso 4: Integrar paquetes y salidas
- Incorporar armado de paquetes, detalle de consumo y registro de salida.
- Validar reglas de stock y consistencia transaccional.

### Paso 5: Reportes unificados
- Consolidar reportes financieros (Transparencia) + operativos (Inventario).
- Unificar exportacion PDF/Excel en un solo modulo.

## Criterios para pasar a PostgreSQL (fase posterior)
- Sin uso de SQL especifico SQLite en logica de negocio.
- Migraciones nuevas compatibles con ambos motores.
- Pruebas de smoke sobre alta donacion -> asignacion -> paquete -> salida.

## Ejecucion de migraciones en Fase 1
- Transparencia (BD principal):  
  `php artisan migrate`
- Inventario (BD separada):  
  `php artisan migrate --database=inventario --path=modulos/donacion-recepcion-inventario-main/database/migrations`
