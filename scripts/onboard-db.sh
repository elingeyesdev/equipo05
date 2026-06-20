#!/usr/bin/env bash
# Onboarding PostgreSQL unificado (Linux/macOS).
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

if [[ ! -f .env ]]; then
  cp .env.example .env
  echo "Creado .env desde .env.example"
fi

echo ">> docker compose up -d db_unificado"
docker compose up -d db_unificado

WAIT="${WAIT_DB:-60}"
ARGS=(artisan db:onboard "--wait-db=${WAIT}")
[[ "${FRESH_INVENTARIO:-}" == "1" ]] && ARGS+=(--fresh-inventario)
[[ "${SEED:-1}" == "1" ]] && ARGS+=(--seed)

echo ">> php ${ARGS[*]}"
php "${ARGS[@]}"

echo ">> verify-unified-modules.php"
php scripts/verify-unified-modules.php

echo "Listo. php artisan serve"
