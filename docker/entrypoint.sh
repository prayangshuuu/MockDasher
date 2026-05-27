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

echo "==> [app] Waiting for database to be ready..."
max_tries=30
count=0
until php artisan db:monitor --max=1 > /dev/null 2>&1 || \
      php -r "
          \$dsn = 'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE');
          try { new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }
      "; do
    count=$((count + 1))
    if [ "$count" -ge "$max_tries" ]; then
        echo "ERROR: Could not connect to database after ${max_tries} attempts. Aborting."
        exit 1
    fi
    echo "  ... database not ready yet (attempt ${count}/${max_tries}), retrying in 2s..."
    sleep 2
done
echo "  ... database is ready!"

echo "==> [app] Running migrations and seeders..."
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

echo "==> [app] Linking storage..."
php artisan storage:link --force 2>/dev/null || true

echo "==> [app] Starting Nginx + PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
