#!/usr/bin/env bash

# Exit on fail
set -e

# Wait for mysql to be ready
# dir=$(dirname "$0")

# $dir/wait-for-it.sh db:3306 -t 300

# Migrate DB
php artisan migrate --force

php artisan storage:link

# Start supervisord
/usr/bin/supervisord -c /etc/supervisord.conf

# Start cronjob
crond

# Start fpm
php-fpm

# Finally call command issued to the docker service
exec "$@"
