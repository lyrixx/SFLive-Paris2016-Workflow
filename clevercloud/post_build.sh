#!/usr/bin/env bash
set -e

./bin/console doctrine:migrations:migrate --no-interaction
