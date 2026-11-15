#!/bin/sh
# Runs at container start, when the real environment variables exist.
cd /var/www/html || exit 0

if [ -z "$APP_KEY" ]; then
    echo "!! APP_KEY is not set. Set it in the Render environment (php artisan key:generate --show) or every page returns 500."
fi

# The database schema is managed in Supabase and the app's database role cannot
# create tables, so migrations are OFF by default. Set RUN_MIGRATIONS=true only
# when connecting with a role that is allowed to run DDL.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force --no-interaction || echo "!! migrate FAILED - check DB_* settings"
fi

php artisan config:cache || echo "config:cache skipped"
php artisan view:cache   || echo "view:cache skipped"

# Create the first admin from ADMIN_EMAIL / ADMIN_PASSWORD (no-op if not set or already there)
php artisan clinic:ensure-admin || echo "clinic:ensure-admin skipped"
