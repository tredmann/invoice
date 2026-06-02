FROM balttech/laravel-octane-base:latest

ARG BUILD_TAG
ARG BUILD_DATE
ARG BUILD_NUMBER
ARG BUILD_COMMIT

RUN touch /var/www/html/.env && chown -R nobody.nobody /var/www/html/.env
RUN echo -e "BUILD_DATE=\"${BUILD_DATE}\"\nBUILD_NUMBER=\"${BUILD_NUMBER}\"\nBUILD_COMMIT=\"${BUILD_COMMIT}\"\nBUILD_TAG=\"${BUILD_TAG}\"\n" > /var/www/html/.env

# Switch to use a non-root user from here on
USER nobody

# Add applica tion
WORKDIR /var/www/html
COPY --chown=nobody ./src /var/www/html/

RUN composer install --no-interaction --no-progress --prefer-dist --no-dev

# Configure a healthcheck to validate that everything is up&running
# HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/health
