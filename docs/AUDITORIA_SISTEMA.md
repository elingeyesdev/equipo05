# Auditoría del sistema — Equipo 05

Fecha de referencia: 19 may 2026 — auditoría estática + pruebas E2E en Docker (`http://localhost`).

## Cómo repetir la auditoría

Con Docker en marcha:

```bash
docker compose exec laravel php artisan qa:audit
docker compose exec laravel php artisan fix:blade-encoding
docker compose exec laravel php artisan view:clear
docker compose exec laravel php artisan storage:link
```

Sin Docker (solo rutas/vistas, sin BD PostgreSQL local):

```bash
php artisan qa:audit
```

---

## Resumen ejecutivo

| Área | Estado | Notas |
|------|--------|-------|
| Rutas → controladores | Corregido | Rutas huérfanas y `apiResource` sin `update`/`destroy` |
| Transparencia ↔ Inventario | Funcional | `UnifiedDataSyncService` + observers |
| Codificación UTF-8 (vistas) | Corregido | Revertidos scripts `Est`/`ltimo`; `fix:blade-encoding` solo FFFD |
| Dashboard principal | Corregido | Variable `$ultimosMensajes` (corrupción `ltimo`) |
| Migraciones módulos Fase 1 | Corregido | Idempotentes si tablas ya existen en PG unificado |
| `php artisan qa:audit` (Docker) | OK | 6/6 comprobaciones, 0 problemas |
| Layout / espaciado UI | Mejorado | `platform-shell.css`, `inventario-module.css` |
| API inventario REST | Corregido | `update` y `destroy` en donaciones |
| Asignación donación | Corregido | Rutas apuntan a métodos reales |
| BD (local sin Docker) | Requiere entorno | Driver `pgsql` + host `db_unificado` en Docker |
| Logística / Seguimiento / Cuadrillas | Parcial | Shell de navegación; lógica completa en módulos originales |
| Incendios / Rescate / Inventario | Completo | CRUD y APIs en `modulos/` |

---

## 1. Transparencia y donaciones (core)

| Función | ¿Cumple? | Observaciones |
|---------|----------|---------------|
| Login / logout | Sí | Sesión en BD (`core`) |
| Usuarios, roles, estados | Sí | CRUD; rutas `show` excluidas (no implementadas) |
| Campañas | Sí | CRUD + espejo a inventario |
| Donaciones monetarias | Sí | Validación `core.usuarios` |
| Asignaciones y saldos | Sí | Asignar donación: rutas corregidas |
| Reportes trazabilidad | Sí | Sync local desde inventario al abrir |
| Gateway / Situaciones | Depende API | `gatealas.dasalas.shop` si está en línea |
| Mensajes / chat | Sí | Pivot `transparencia.conversacion_usuarios` |
| Cierre caja / PDF Excel | Sí | Requiere datos en BD |

---

## 2. Inventario

| Función | ¿Cumple? | Observaciones |
|---------|----------|---------------|
| Almacenes, estantes, espacios | Sí | Sync → `ext_almacenes` |
| Donantes, productos, categorías | Sí | UTF-8 corregido en listados |
| Donaciones especie/dinero | Sí | Observer actualiza trazabilidad |
| Paquetes, salidas, solicitudes | Sí | |
| Reportes inventario | Sí | |
| API REST donaciones | Sí | `update` / `destroy` añadidos |
| API trazabilidad voluntario | Sí | Rutas públicas documentadas |

---

## 3. Incendios

| Función | ¿Cumple? | Observaciones |
|---------|----------|---------------|
| Dashboard / clima | Sí | |
| Focos FIRMS | Sí | `firms:update` programado |
| Simulaciones / predicciones | Sí | |
| Usuarios / biomasas / voluntarios | Sí | Validación email en `core.usuarios` si PG unificado |
| API Sanctum | Sí | |

---

## 4. Rescate

| Función | ¿Cumple? | Observaciones |
|---------|----------|---------------|
| Reportes / animales / centros | Sí | |
| Cuidado, liberaciones, traslados | Sí | |
| API + trazabilidad | Sí | |
| FIRMS / focos calor | Sí | Comandos programados |

---

## 5. Logística, seguimiento, cuadrillas

| Función | ¿Cumple? | Observaciones |
|---------|----------|---------------|
| Menú y vistas sección | Parcial | Placeholders integrados en Fase 1 |
| API health | Sí | `/api/logistica/health`, etc. |
| Público solicitud ayuda | Sí | Rutas en core |

---

## Errores corregidos en esta auditoría

1. **Rutas `asignarDonacionForm` / `asignarDonacionStore`** → enlazadas a `asignar` y `guardarAsignacion`.
2. **Resources sin método `show`** → `->except(['show'])` para evitar 500 al acceder a URLs REST no implementadas.
3. **API `DonacionController`** → métodos `update` y `destroy` implementados.
4. **Vistas con ``** → comando `fix:blade-encoding`.
5. **Locale** → español por defecto en `config/app.php`.
6. **Auditoría automatizada** → `php artisan qa:audit`.

---

## Requisitos de entorno para prueba completa

1. `docker compose up -d`
2. `docker compose exec laravel php artisan config:clear`
3. Setup BD (primera vez): `db:setup-unificado`, `db:consolidate-users-core`, `db:seed`
4. `docker compose exec laravel php artisan sync:unificado-local`
5. Login: usuario demo documentado en README

---

## Próximos pasos recomendados (no bloqueantes)

- Tests Pest por módulo (CRUD críticos con `RefreshDatabase` en PG de prueba).
- Completar port de lógica en Logística / Seguimiento / Cuadrillas si se requiere paridad con proyectos fuente.
- CI: `php artisan qa:audit` en pipeline tras `composer install`.
