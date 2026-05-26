# ─── Stage 1: Build Vite/Tailwind assets ──────────────────────────────────────
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY vite.config.js ./
COPY resources/ resources/
COPY public/ public/

RUN npm run build


# ─── Stage 2: PHP production image ────────────────────────────────────────────
FROM php:8.4-fpm-alpine AS app

# System deps for the PHP extensions we're compiling
RUN apk add --no-cache \
        bash \
        curl \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        nginx \
        supervisor \
        unzip \
        zip

# Only install extensions NOT already bundled in php:8.2-fpm-alpine.
# Already included: ctype, dom, fileinfo, mbstring, opcache, tokenizer, xml, xmlwriter
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        pdo_mysql \
        zip

# Composer binary from official image
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP deps first (separate layer — cache-friendly)
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-scripts \
        --prefer-dist

# Copy application source (vendor excluded via .dockerignore)
COPY . .

# Regenerate the optimized autoloader now that the full app source is present
RUN composer dump-autoload --optimize --no-dev --no-interaction --no-scripts

# Overlay built Vite assets from Stage 1
COPY --from=assets /app/public/build public/build/

# Place config files
COPY docker/php/php.ini        /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini    /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-fpm.conf   /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf   /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh      /entrypoint.sh

# Ensure Laravel writable directories exist and are owned by www-data
RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        storage/logs \
        bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
