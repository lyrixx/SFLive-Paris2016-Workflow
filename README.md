SFLive-Paris2016-Workflow
=========================

Installation
------------

    composer install
    bin/console doctrine:database:create
    bin/console doctrine:schema:update --force

If you update the workflow configuration, you will need to regenerate the
SVG by running the following command:

    # For the task
    bin/console  workflow:build:svg state_machine.task
    # For the article
    bin/console  workflow:build:svg workflow.article
