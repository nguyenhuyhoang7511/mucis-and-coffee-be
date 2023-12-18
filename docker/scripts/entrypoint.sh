#!/usr/bin/env bash

# Exit on fail
set -e

# Install packagist
composer install

# Wait for mysql to be ready
dir=$(dirname "$0")

$dir/wait-for-it.sh db:3306 -t 300

# Migrate DB
php artisan migrate

# Start supervisord
/usr/bin/supervisord -c /etc/supervisord.conf

# Start cronjob
crond

# Start serve
php artisan serve --host=0.0.0.0

# Finally call command issued to the docker service
exec "$@"
