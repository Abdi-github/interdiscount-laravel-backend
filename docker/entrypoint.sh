#!/bin/sh
set -e

# Cache config/routes/views at runtime (when env vars are available)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations if needed
php artisan migrate --force --no-interaction 2>/dev/null || true

# Seed database if admins table is empty (fresh deploy)
ADMIN_COUNT=$(php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('admins')->count();" 2>/dev/null || echo "0")
if [ "$ADMIN_COUNT" = "0" ]; then
    php artisan db:seed --force --no-interaction 2>/dev/null || true
fi

# Execute the main container command (php-fpm)
exec "$@"
