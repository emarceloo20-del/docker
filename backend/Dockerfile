# syntax=docker/dockerfile:1.7

# ---------- Etapa 1: dependencias de Composer ----------
FROM composer:2 AS vendor
WORKDIR /app
# Capa cacheable: solo se invalida si cambian composer.json / composer.lock
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/cache \
    composer install --no-dev --no-interaction --no-progress --prefer-dist \
      --no-scripts --no-autoloader
# Ahora el código, para generar el autoloader optimizado
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# ---------- Etapa 2: runtime (PHP-FPM + Nginx) ----------
FROM php:8.3-fpm-alpine AS runtimew

# install-php-extensions resuelve dependencias de compilación y limpia después.
# openssl y json ya vienen compiladas en la imagen oficial; se instalan las demás.
COPY --from=ghcr.io/mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN apk add --no-cache nginx supervisor \
 && install-php-extensions pdo_mysql mbstring xml ctype tokenizer opcache \
 && rm /usr/local/bin/install-php-extensions \
 && php -m | grep -Ei 'pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json' \
 && mkdir -p /run/nginx

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-env.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html
# Código de la app (sin vendor, excluido por .dockerignore) + vendor desde la etapa de build
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
             storage/logs storage/api-docs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
  CMD wget -qO- http://127.0.0.1/up >/dev/null || exit 1

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
