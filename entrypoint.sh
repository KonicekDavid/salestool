#!/bin/sh
set -e

chown -R www-data:www-data /var/www/salestool/temp /var/www/salestool/log || true
chmod -R 777 /var/www/salestool/temp /var/www/salestool/log || true

composer install

DB_FILE="/var/www/salestool/db/database.sqlite"
SQL_SCHEMA="/var/www/salestool/schema.sql"

if [ ! -f "$DB_FILE" ]; then
    echo "Creating SQLite database..."
    mkdir -p "$(dirname "$DB_FILE")"
    sqlite3 "$DB_FILE" < "$SQL_SCHEMA"
else
    echo "SQLite database already exists."
fi

chown -R www-data:www-data /var/www/salestool/db
chmod -R 775 /var/www/salestool/db

exec php-fpm