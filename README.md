SFLive-Paris2016-Workflow
=========================

Installation
------------

    composer install
    rm -rf vendor/symfony/symfony
    git clone git@github.com:lyrixx/symfony vendor/symfony/symfony --branch component-workflow
    bin/console doctrine:database:create
    bin/console doctrine:schema:update --force

