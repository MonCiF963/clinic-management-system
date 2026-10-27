#!/bin/sh
# Runs at container start, when the real environment variables exist.
cd /var/www/html || exit 0
php artisan config:cache || echo "config:cache skipped"
php artisan view:cache   || echo "view:cache skipped"
