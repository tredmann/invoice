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
