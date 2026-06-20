Imágenes de referencia por especie para el módulo de rescate de fauna silvestre.

- Se versionan en git para que el equipo vea fotos tras `git pull` sin depender de Wikimedia.
- Tras clonar o actualizar, ejecutar: `php artisan rescate:ensure-media --sync-db`
- Las rutas en BD (`imagen_url`) se resuelven vía `rescate_media_url()` con fallback a este catálogo.
