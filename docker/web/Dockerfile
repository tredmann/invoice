FROM serversideup/php:8.5-frankenphp

USER root

RUN install-php-extensions gd imagick xdebug

# Node + chokidar for Laravel Octane file watching
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/* \
    && npm install -g chokidar@^3.5

COPY --chmod=644 docker/web/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini
COPY --chmod=755 docker/web/dev-entrypoint.sh /usr/local/bin/dev-entrypoint.sh

USER www-data

CMD ["dev-entrypoint.sh"]
