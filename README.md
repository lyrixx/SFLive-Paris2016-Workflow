SFLive-Paris2016-Workflow
=========================

Installation
------------

    composer install
    bin/console doctrine:database:create
    bin/console doctrine:schema:update --force

And you need to apply [this patch](https://github.com/symfony/symfony/pull/18425)
