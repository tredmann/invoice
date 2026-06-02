FROM serversideup/php:8.5-frankenphp

ARG BUILD_TAG
ARG BUILD_DATE
ARG BUILD_NUMBER
ARG BUILD_COMMIT
ARG NR_KEY

USER root

# Runtime PHP extensions (no xdebug in prod)
RUN install-php-extensions gd imagick

# mysqldump (for spatie/laravel-backup)
RUN apt-get update \
    && apt-get install -y --no-install-recommends default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Production-tuned PHP settings (opcache enabled, no timestamp revalidation)
ENV PHP_OPCACHE_ENABLE=1 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_REVALIDATE_FREQ=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=20000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=256 \
    APP_ENV=production \
    HEALTHCHECK_PATH=/

COPY --chmod=755 docker/web/prod-entrypoint.sh /usr/local/bin/prod-entrypoint.sh

WORKDIR /var/www/html

COPY --chown=www-data:www-data ./src /var/www/html/

# Run composer install as www-data so file ownership is correct.
USER www-data
# --no-scripts on the install pass keeps build hermetic (no .env / APP_KEY needed
#   at install time). The subsequent dump-autoload triggers post-autoload-dump
#   which runs `artisan package:discover` once classes are wired up.
# --ignore-platform-req=php+ ignores upper-bound PHP version constraints from
#   locked packages (phpoffice/phpspreadsheet 1.30.5 declares "<8.5.0" but runs
#   fine on 8.5.x — confirmed in dev runtime).
RUN composer install --no-interaction --no-progress --prefer-dist --no-dev --no-scripts \
        --optimize-autoloader --ignore-platform-req=php+ \
    && composer dump-autoload --optimize --classmap-authoritative

# Write build metadata into the image's .env (real config comes from runtime secrets).
RUN printf 'BUILD_DATE="%s"\nBUILD_NUMBER="%s"\nBUILD_COMMIT="%s"\nBUILD_TAG="%s"\nNR_KEY="%s"\n' \
    "${BUILD_DATE}" "${BUILD_NUMBER}" "${BUILD_COMMIT}" "${BUILD_TAG}" "${NR_KEY}" > /var/www/html/.env

CMD ["prod-entrypoint.sh"]
