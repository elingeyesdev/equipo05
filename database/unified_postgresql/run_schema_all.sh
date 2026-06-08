#!/usr/bin/env bash
# Aplica scripts SQL del esquema unificado. Requiere psql.
# Uso: export PGPASSWORD=...  (opcional)
#      ./run_schema_all.sh [host] [puerto] [usuario] [base]
set -euo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PGHOST="${1:-${PGHOST:-127.0.0.1}}"
PGPORT="${2:-${PGPORT:-5432}}"
PGUSER="${3:-${PGUSER:-postgres}}"
PGDATABASE="${4:-${PGDATABASE:-equipo05_unificado}}"
export PGHOST PGPORT PGUSER PGDATABASE

ORDERED=(
  "01_extensions_and_schemas.sql"
  "00_core_auth.sql"
  "04_mod_inventario_transparencia.sql"
  "02_mod_incendios.sql"
  "03_mod_rescate.sql"
  "03b_mod_rescate_transfers_persona.sql"
  "06_mod_logistica.sql"
  "07_mod_seguimiento.sql"
  "08_mod_cuadrillas.sql"
)

for f in "${ORDERED[@]}"; do
  echo ">> ${f}"
  psql -v ON_ERROR_STOP=1 -f "${HERE}/${f}"
done

echo "Listo: esquemas aplicados en ${PGDATABASE}"
