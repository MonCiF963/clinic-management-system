#!/bin/sh
# Runs at container start, when the real environment variables exist.
cd /var/www/html || exit 0

if [ -z "$APP_KEY" ]; then
    echo "!! APP_KEY is not set. Set it in the Render environment (php artisan key:generate --show) or every page returns 500."
fi

# Sessions and cache use the database by default, so the tables must exist
# before the first request. Without this every page returns a 500 error.
php artisan migrate --force --no-interaction || echo "!! migrate FAILED - check DB_* / DATABASE_URL settings"

php artisan config:cache || echo "config:cache skipped"
php artisan view:cache   || echo "view:cache skipped"
