#!/bin/bash
set -e

cd /var/www/html

if [[ "${1:-}" == "worker" ]]; then
    echo "==> [worker] Starting queue worker..."
    exec php artisan queue:work \
        --tries=3 \
        --timeout=120 \
        --sleep=3 \
        --max-time=3600
fi

echo "==> [app] Warming Laravel caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> [app] Running migrations and seeders..."
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

echo "==> [app] Linking storage..."
php artisan storage:link --force 2>/dev/null || true

echo "==> [app] Starting Nginx + PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
