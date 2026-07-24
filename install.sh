#!/usr/bin/env bash
set -Eeuo pipefail

PROJECT_NAME="lince_mvc"
DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_NAME="lince_motos"
DB_USER="root"
DB_PASS=""
TARGET_DIR=""
RESET_DB=0
SKIP_DB=0

usage() {
  cat <<'EOF'
Instalador del Sistema MVC de Motocicletas LINCE para XAMPP.

Uso:
  bash install.sh [opciones]

Opciones:
  --target RUTA       Carpeta final del proyecto dentro de htdocs.
  --db-host HOST      Servidor MySQL. Predeterminado: 127.0.0.1
  --db-port PUERTO    Puerto MySQL. Predeterminado: 3306
  --db-name NOMBRE    Base de datos. Predeterminado: lince_motos
  --db-user USUARIO   Usuario MySQL. Predeterminado: root
  --db-pass CLAVE     Contraseña MySQL. Predeterminado: vacía
  --reset-db           Elimina y vuelve a crear las tablas del prototipo.
  --skip-db            Instala archivos sin importar ni actualizar la base de datos.
  -h, --help           Muestra esta ayuda.

Ejemplos:
  bash install.sh
  bash install.sh --db-user root --db-pass "mi_clave"
  bash install.sh --target /opt/lampp/htdocs/lince_mvc
  bash install.sh --reset-db
EOF
}

fail() {
  printf '\nERROR: %s\n' "$1" >&2
  exit 1
}

info() {
  printf '==> %s\n' "$1"
}

warn() {
  printf 'AVISO: %s\n' "$1" >&2
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --target) TARGET_DIR="${2:-}"; shift 2 ;;
    --db-host) DB_HOST="${2:-}"; shift 2 ;;
    --db-port) DB_PORT="${2:-}"; shift 2 ;;
    --db-name) DB_NAME="${2:-}"; shift 2 ;;
    --db-user) DB_USER="${2:-}"; shift 2 ;;
    --db-pass) DB_PASS="${2:-}"; shift 2 ;;
    --reset-db) RESET_DB=1; shift ;;
    --skip-db) SKIP_DB=1; shift ;;
    -h|--help) usage; exit 0 ;;
    *) fail "Opción no reconocida: $1" ;;
  esac
done

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [[ -z "$TARGET_DIR" ]]; then
  if [[ -d /c/xampp/htdocs ]]; then
    TARGET_DIR="/c/xampp/htdocs/$PROJECT_NAME"
  elif [[ -d /mnt/c/xampp/htdocs ]]; then
    TARGET_DIR="/mnt/c/xampp/htdocs/$PROJECT_NAME"
  elif [[ -d /opt/lampp/htdocs ]]; then
    TARGET_DIR="/opt/lampp/htdocs/$PROJECT_NAME"
  else
    fail "No se encontró XAMPP. Usa --target con la ruta de htdocs."
  fi
fi

mkdir -p "$(dirname "$TARGET_DIR")"

SOURCE_REAL="$(cd "$SCRIPT_DIR" && pwd -P)"
TARGET_PARENT="$(cd "$(dirname "$TARGET_DIR")" && pwd -P)"
TARGET_REAL="$TARGET_PARENT/$(basename "$TARGET_DIR")"

if [[ "$SOURCE_REAL" != "$TARGET_REAL" ]]; then
  info "Copiando el proyecto a $TARGET_DIR"
  mkdir -p "$TARGET_DIR"
  if command -v rsync >/dev/null 2>&1; then
    rsync -a --delete --exclude='.git/' --exclude='app/config/config.local.php' --exclude='public/assets/js/qrcode.min.js' --exclude='public/uploads/motos/*.jpg' --exclude='public/uploads/motos/*.png' --exclude='public/uploads/motos/*.webp' "$SCRIPT_DIR/" "$TARGET_DIR/"
  else
    find "$TARGET_DIR" -mindepth 1 -maxdepth 1 ! -name public -exec rm -rf {} +
    (cd "$SCRIPT_DIR" && tar --exclude='./.git' --exclude='./app/config/config.local.php' --exclude='./public/assets/js/qrcode.min.js' --exclude='./public/uploads/motos/*.jpg' --exclude='./public/uploads/motos/*.png' --exclude='./public/uploads/motos/*.webp' -cf - .) | (cd "$TARGET_DIR" && tar -xf -)
  fi
else
  info "El proyecto ya está ubicado en $TARGET_DIR"
fi

mkdir -p "$TARGET_DIR/public/uploads/motos" "$TARGET_DIR/public/assets/js"

CONFIG_FILE="$TARGET_DIR/app/config/config.local.php"
php_export() {
  local value="$1"
  value="${value//\\/\\\\}"
  value="${value//\'/\\\'}"
  printf "'%s'" "$value"
}

cat > "$CONFIG_FILE" <<PHP
<?php
declare(strict_types=1);

return [
    'db_host' => $(php_export "$DB_HOST"),
    'db_port' => $(php_export "$DB_PORT"),
    'db_name' => $(php_export "$DB_NAME"),
    'db_user' => $(php_export "$DB_USER"),
    'db_pass' => $(php_export "$DB_PASS"),
];
PHP

info "Configuración local creada en app/config/config.local.php"

QR_LIBRARY="$TARGET_DIR/public/assets/js/qrcode.min.js"
QR_LIBRARY_URL="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"
if [[ ! -s "$QR_LIBRARY" ]]; then
  info "Instalando generador local de códigos QR"
  QR_TEMP="$QR_LIBRARY.tmp"
  if command -v curl >/dev/null 2>&1; then
    curl -fsSL "$QR_LIBRARY_URL" -o "$QR_TEMP" && mv "$QR_TEMP" "$QR_LIBRARY" || rm -f "$QR_TEMP"
  elif command -v wget >/dev/null 2>&1; then
    wget -qO "$QR_TEMP" "$QR_LIBRARY_URL" && mv "$QR_TEMP" "$QR_LIBRARY" || rm -f "$QR_TEMP"
  else
    warn "No se encontró curl ni wget. El sistema intentará cargar el generador QR desde Internet al abrir el expediente."
  fi
fi

if [[ -s "$QR_LIBRARY" ]]; then
  info "Generador QR disponible localmente"
else
  warn "No se pudo descargar qrcode.min.js. Puede ejecutar nuevamente el instalador cuando tenga conexión."
fi

find_binary() {
  local name="$1"
  shift
  if command -v "$name" >/dev/null 2>&1; then
    command -v "$name"
    return 0
  fi
  local candidate
  for candidate in "$@"; do
    if [[ -x "$candidate" || -f "$candidate" ]]; then
      printf '%s\n' "$candidate"
      return 0
    fi
  done
  return 1
}

PHP_BIN="$(find_binary php \
  /c/xampp/php/php.exe \
  /mnt/c/xampp/php/php.exe \
  /opt/lampp/bin/php || true)"

[[ -n "$PHP_BIN" ]] || fail "No se encontró PHP de XAMPP."

info "Validando archivos PHP"
while IFS= read -r -d '' file; do
  "$PHP_BIN" -l "$file" >/dev/null || fail "Error de sintaxis en $file"
done < <(find "$TARGET_DIR" -type f -name '*.php' -print0)

if [[ "$SKIP_DB" -eq 0 ]]; then
  MYSQL_BIN="$(find_binary mysql \
    /c/xampp/mysql/bin/mysql.exe \
    /mnt/c/xampp/mysql/bin/mysql.exe \
    /opt/lampp/bin/mysql || true)"

  [[ -n "$MYSQL_BIN" ]] || fail "No se encontró el cliente MySQL de XAMPP."

  MYSQL_ARGS=(--protocol=tcp -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" --default-character-set=utf8mb4)
  export MYSQL_PWD="$DB_PASS"

  "$MYSQL_BIN" "${MYSQL_ARGS[@]}" -e "SELECT 1;" >/dev/null 2>&1 \
    || fail "No fue posible conectar a MySQL. Verifica que MySQL esté iniciado y que las credenciales sean correctas."

  TABLE_COUNT="$("$MYSQL_BIN" "${MYSQL_ARGS[@]}" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DB_NAME';")"

  if [[ "$TABLE_COUNT" -gt 0 && "$RESET_DB" -eq 0 ]]; then
    info "La base $DB_NAME ya contiene tablas; se conservaron los datos."
  else
    info "Creando e importando la base de datos $DB_NAME"
    "$MYSQL_BIN" "${MYSQL_ARGS[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

    SQL_TEMP="$(mktemp)"
    trap 'rm -f "$SQL_TEMP"' EXIT
    sed -E \
      -e '/^CREATE DATABASE IF NOT EXISTS lince_motos /d' \
      -e '/^USE lince_motos;/d' \
      "$TARGET_DIR/database/lince_motos.sql" > "$SQL_TEMP"

    "$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" < "$SQL_TEMP"
    rm -f "$SQL_TEMP"
    trap - EXIT
    info "Base de datos importada correctamente"
  fi

  "$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" -e "CREATE TABLE IF NOT EXISTS schema_migrations (migration VARCHAR(190) PRIMARY KEY, applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;"

  if compgen -G "$TARGET_DIR/database/migrations/*.sql" >/dev/null; then
    for migration_file in "$TARGET_DIR"/database/migrations/*.sql; do
      migration_name="$(basename "$migration_file")"
      applied="$("$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" -Nse "SELECT COUNT(*) FROM schema_migrations WHERE migration = '$migration_name';")"
      if [[ "$applied" -eq 0 ]]; then
        info "Aplicando actualización de base: $migration_name"
        "$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" < "$migration_file"
        "$MYSQL_BIN" "${MYSQL_ARGS[@]}" "$DB_NAME" -e "INSERT INTO schema_migrations (migration) VALUES ('$migration_name');"
      fi
    done
  fi

  unset MYSQL_PWD
else
  info "Importación y actualización de MySQL omitidas por --skip-db"
fi

printf '%s\n' "Instalado: $(date -u +'%Y-%m-%dT%H:%M:%SZ')" > "$TARGET_DIR/.installed"

FOLDER_NAME="$(basename "$TARGET_DIR")"
printf '\nInstalación completada.\n'
printf 'Abra Apache y MySQL desde XAMPP y visite:\n'
printf '  http://localhost/%s/\n\n' "$FOLDER_NAME"
printf 'Usuario inicial:\n'
printf '  admin@lince.local\n'
printf '  Admin1234\n\n'
printf 'Cambie la contraseña antes de utilizar datos institucionales.\n'
