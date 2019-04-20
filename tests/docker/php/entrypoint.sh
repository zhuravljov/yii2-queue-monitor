#!/bin/sh

set -eu

# Installs Composer packages
flock tests/runtime/composer-install.lock composer install --prefer-dist --no-interaction

# Waits for MySQL and applies DB migrations
tests/docker/wait-for-it.sh mysql:3306 -t 180
tests/docker/php/mysql-lock.php php tests/yii migrate/up --interactive=0

# Executes container command
set -x
exec "$@"
