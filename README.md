# SFLive-Paris2016-Workflow

Demo application of the [symfony/workflow](https://symfony.com/doc/current/components/workflow.html) component.

## Running the application locally

### Requirements

A Docker environment is provided and requires you to have these tools available:

 * Docker
 * Bash
 * [Castor](https://github.com/jolicode/castor#installation)

Once `castor` is installed, you can install its console autocompletion script
(`bash`, `zsh` and `fish` are supported):

```bash
castor completion | sudo tee /etc/bash_completion.d/castor
```

### Docker environment

The Docker infrastructure provides a web stack with:
 - NGINX
 - PostgreSQL
 - PHP
 - Traefik
 - A container with some tooling (Composer, Node, Yarn / NPM)

### Domain configuration (first time only)

Before running the application for the first time, ensure the domain name
points to the IP of your Docker daemon by editing your `/etc/hosts` file:

```bash
echo '127.0.0.1 workflow.test' | sudo tee -a /etc/hosts
```

### Starting the stack

```bash
castor start
```

The application is then available at https://workflow.test (the first start
takes a few minutes).

HTTPS is supported out of the box: SSL certificates are generated the first
time you start the infrastructure, with [`mkcert`](https://github.com/FiloSottile/mkcert#installation)
if it is installed (locally trusted certificates, do not forget `mkcert -install`),
or self-signed with `openssl` otherwise. Run `castor docker:generate-certificates --force`
to regenerate them.

This stack supports [git worktrees](https://git-scm.com/docs/git-worktree) out
of the box: in a worktree, the infrastructure is fully isolated (project name,
volumes, networks, ports). Run `castor docker:ports` to see the allocated ports.

### Builder

Composer, the Symfony console and the other tools run in the builder container:

```bash
castor builder                               # interactive shell
castor builder -- bin/console debug:router   # one-off command
```

### Production images

The "Production stages" of `infrastructure/docker/services/php/Dockerfile`
build two self-contained images, `php` and `nginx`. They are not published
(the demo is hosted on Clever Cloud), but they can be tested locally, on a
stack independent from the development one:

```bash
castor start -c prod       # -> http://127.0.0.1:8000
castor destroy -c prod
```

### Other tasks

Run `castor` to list the available tasks (QA: `castor qa:all`, database:
`castor migrate`, `castor postgres`...).

## Workflow diagrams

If you update the workflow configuration, you will need to regenerate the
SVG by running the following command:

    # For the task
    castor builder -- bin/console workflow:build:svg state_machine.task
    # For the article
    castor builder -- bin/console workflow:build:svg workflow.article

## Thanks

Thanks [CleverCloud](https://www.clever-cloud.com/) for hosting the [demo
application](https://demo-symfony-workflow.cleverapps.io/).
