# ---- Stage 1: build front-end assets ----
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package*.json ./
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
COPY . .
RUN npm run build

# ---- Stage 2: PHP dependencies, no dev packages ----
FROM serversideup/php:8.5-cli AS vendor
WORKDIR /var/www/html
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY --chown=www-data:www-data . .
RUN composer dump-autoload --optimize --no-dev

# ---- Stage 3: production image ----
FROM serversideup/php:8.5-fpm-nginx
ENV PHP_OPCACHE_ENABLE=1
USER root
RUN install-php-extensions pdo_pgsql
USER www-data
WORKDIR /var/www/html
COPY --chown=www-data:www-data --from=vendor /var/www/html /var/www/html
COPY --chown=www-data:www-data --from=assets /app/public/build /var/www/html/public/build
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
COPY --chmod=755 docker/entrypoint.d/50-laravel-cache.sh /etc/entrypoint.d/50-laravel-cache.sh
