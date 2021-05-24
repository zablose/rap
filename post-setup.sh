#!/usr/bin/env bash

set -e

cd ${DAMP_WEB_DIR}

php /home/${DAMP_USER_NAME}/bin/composer update

php laravel/artisan key:generate --ansi
php laravel/artisan vendor:publish --provider="Zablose\Rap\RapServiceProvider" --tag=migrations

php vendor/bin/phpunit
