#!/usr/bin/env bash
# Ejecutado solo la primera vez que se crea el volumen de datos (imagen oficial PostgreSQL).
# Orden alineado con database/unified_postgresql/run_schema_all.sh
set -euo pipefail

SQL_DIR="${SQL_DIR:-/docker-entrypoint-sql}"

for f in \
  01_extensions_and_schemas.sql \
  04_mod_inventario_transparencia.sql \
  02_mod_incendios.sql \
  03_mod_rescate.sql \
  06_mod_logistica.sql \
  07_mod_seguimiento.sql \
  08_mod_cuadrillas.sql
do
  echo ">> init-unificado-db: ${f}"
  psql -v ON_ERROR_STOP=1 -U "${POSTGRES_USER}" -d "${POSTGRES_DB}" -f "${SQL_DIR}/${f}"
done

echo ">> init-unificado-db: listo"
