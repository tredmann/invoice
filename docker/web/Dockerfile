FROM serversideup/php:8.5-frankenphp

USER root

RUN install-php-extensions gd imagick xdebug

# WeasyPrint (Python-based PDF renderer) + runtime libs + base fonts.
# We install via apt for reproducibility; the build fails fast if the binary is broken.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        python3 \
        python3-pip \
        weasyprint \
        libpango-1.0-0 \
        libpangoft2-1.0-0 \
        libharfbuzz0b \
        libcairo2 \
        libgdk-pixbuf-2.0-0 \
        shared-mime-info \
        fonts-dejavu \
        fonts-noto \
        fonts-liberation \
    && rm -rf /var/lib/apt/lists/* \
    && weasyprint --version

# mysqldump (for spatie/laravel-backup)
RUN apt-get update \
    && apt-get install -y --no-install-recommends default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Node + chokidar for Laravel Octane file watching
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/* \
    && npm install -g chokidar@^3.5

COPY --chmod=644 docker/web/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini
COPY --chmod=755 docker/web/dev-entrypoint.sh /usr/local/bin/dev-entrypoint.sh

USER www-data

CMD ["dev-entrypoint.sh"]
