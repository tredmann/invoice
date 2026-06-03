#!/bin/bash
set -u

APP_BASE_DIR="${APP_BASE_DIR:-/var/www/html}"

QUEUE_PID=""
SCHEDULE_PID=""
WEB_PID=""

term_handler() {
    trap - TERM INT QUIT
    echo "▶ Shutting down workers and web server..."
    for pid in "$QUEUE_PID" "$SCHEDULE_PID" "$WEB_PID"; do
        if [ -n "$pid" ]; then
            kill -TERM "$pid" 2>/dev/null || true
        fi
    done
}

trap term_handler TERM INT QUIT

echo "▶ Waiting for database..."
RETRIES=0
until mysql --skip-ssl -h "${DB_HOST}" -u "${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" -e "SELECT 1" > /dev/null 2>&1; do
    RETRIES=$((RETRIES + 1))
    if [ "$RETRIES" -ge 30 ]; then
        echo ""
        echo "✖ Database not ready after 60s. Check DB_HOST/DB_USERNAME/DB_PASSWORD/DB_DATABASE in src/.env"
        exit 1
    fi
    printf '.'
    sleep 2
done
echo ""

echo "▶ Running migrations..."
php "$APP_BASE_DIR/artisan" migrate --no-interaction

echo "▶ Starting Laravel queue worker..."
php "$APP_BASE_DIR/artisan" queue:work --tries=3 --sleep=3 &
QUEUE_PID=$!

echo "▶ Starting Laravel scheduler..."
php "$APP_BASE_DIR/artisan" schedule:work &
SCHEDULE_PID=$!

echo "▶ Starting Laravel Octane (FrankenPHP)..."
php "$APP_BASE_DIR/artisan" octane:frankenphp \
    --host=0.0.0.0 \
    --port=8080 \
    --admin-port=2019 \
    --workers=auto \
    --max-requests=500 \
    --watch &
WEB_PID=$!

wait -n
EXIT_CODE=$?

term_handler
wait
exit $EXIT_CODE
