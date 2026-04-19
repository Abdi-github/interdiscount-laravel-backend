#!/bin/sh
set -e

# Cache config/routes/views at runtime (when env vars are available)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations if needed
php artisan migrate --force --no-interaction 2>/dev/null || true

# Execute the main container command (php-fpm)
exec "$@"
