# SFLive-Paris2016-Workflow

Demo application of the [symfony/workflow](https://symfony.com/doc/current/components/workflow.html) component.

## Installation

    composer install
    # edit the .env file
    bin/console doctrine:database:create
    bin/console doctrine:migration:migrate

If you update the workflow configuration, you will need to regenerate the
SVG by running the following command:

    # For the task
    bin/console  workflow:build:svg state_machine.task
    # For the article
    bin/console  workflow:build:svg workflow.article

## Thanks

Thanks [CleverCloud](https://www.clever-cloud.com/) for hosting the [demo
application](https://demo-symfony-workflow.cleverapps.io/).
