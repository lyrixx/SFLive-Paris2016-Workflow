#!/usr/bin/env bash
set -e

./bin/console importmap:install
./bin/console asset-map:compile
./bin/console doctrine:migrations:migrate --no-interaction
