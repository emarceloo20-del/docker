#!/bin/sh
set -e
cd /var/www/html

# Cachés en runtime, cuando ya existen las variables de entorno.
# route:cache puede fallar si hay rutas con closures (routes/web.php): no es crítico.
if [ -n "$APP_KEY" ]; then
  php artisan config:cache
  php artisan route:cache || echo "route:cache omitido (hay rutas con closures)"
  php artisan view:cache || true
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
  php artisan migrate --force
fi

exec "$@"
