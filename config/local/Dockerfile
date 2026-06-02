FROM alpine:latest

RUN apk add --no-cache \
      php8 php8-fpm php8-mysqli php8-json php8-openssl php8-curl php8-iconv \
      php8-zlib php8-xml php8-phar php8-intl php8-dom php8-xmlreader php8-ctype php8-session \
      php8-mbstring php8-gd php8-common php8-pdo php8-pdo_mysql php8-pdo_sqlite \
      php8-tokenizer php8-opcache php8-pear php8-pecl-xdebug php8-fileinfo php8-zip php8-simplexml php8-xmlwriter \
      php8-pcntl php8-posix php8-sqlite3 php8-pecl-imagick

# Install packages
RUN apk --no-cache add nginx supervisor curl tzdata imagemagick-dev libpng imagemagick mysql-client

RUN cp /usr/share/zoneinfo/Europe/Berlin /etc/localtime && echo "Europe/Berlin" > /etc/timezone

# composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
#RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --2 --install-dir=/usr/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

# Configure nginx
COPY config/local/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY config/local/fpm-pool.conf /etc/php8/php-fpm.d/www.conf
COPY config/local/php.ini /etc/php8/conf.d/zzz_custom.ini

# Configure supervisord
COPY config/local/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# CRON for scheduler
RUN { cat; echo "* * * * * cd /var/www/html && php artisan schedule:run >> /var/www/html/storage/logs/scheduler.log &"; } >> /crontab.txt && chown -R nobody.nobody /crontab.txt
ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.9/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=5ddf8ea26b56d4a7ff6faecdd8966610d5cb9d85

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

# setup for tinker
RUN mkdir -p //.config/psysh && mkdir -p /var/tmp/nginx && \
    chmod -R 755 //.config && \
    chown -R nobody:nobody //.config

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/tmp/nginx && \
  chown -R nobody.nobody /var/log/nginx

COPY ./config/local/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Switch to use a non-root user from here on
USER nobody

# Add application
WORKDIR /var/www/html

# Expose the port nginx is reachable on
EXPOSE 8080


# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping

# just local env
ENTRYPOINT sh /entrypoint.sh && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
