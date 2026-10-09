#!/bin/sh
# Backup database MySQL dan .env aplikasi short URL.
# Contoh cron (setiap hari 02:30):
#   30 2 * * * /DATA/AppData/Bitly/scripts/backup.sh >> /DATA/AppData/Bitly/backups/backup.log 2>&1
set -eu

PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-$PROJECT_DIR/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"
STAMP="$(date +%Y%m%d-%H%M%S)"

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
cd "$PROJECT_DIR"

DB_FILE="$BACKUP_DIR/db-$STAMP.sql.gz"
docker compose exec -T db sh -c 'exec mysqldump --single-transaction --quick --routines --no-tablespaces -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' 2>/dev/null \
    | gzip -9 > "$DB_FILE.tmp"

# Pastikan dump tidak kosong / terpotong sebelum dianggap berhasil.
if ! gzip -t "$DB_FILE.tmp" || ! zcat "$DB_FILE.tmp" | tail -n 1 | grep -q "Dump completed"; then
    rm -f "$DB_FILE.tmp"
    echo "[$STAMP] GAGAL: dump database tidak valid" >&2
    exit 1
fi
mv "$DB_FILE.tmp" "$DB_FILE"

install -m 600 .env "$BACKUP_DIR/env-$STAMP"
chmod 600 "$DB_FILE"

find "$BACKUP_DIR" -maxdepth 1 -type f \( -name 'db-*.sql.gz' -o -name 'env-*' \) -mtime +"$RETENTION_DAYS" -delete

echo "[$STAMP] OK: $(du -h "$DB_FILE" | cut -f1) -> $DB_FILE"
