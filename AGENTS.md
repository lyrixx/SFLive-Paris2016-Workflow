# AGENTS.md

Instructions for AI agents working on this project.

## Fundamental rule: never run PHP/Node/Composer on the host machine

Everything goes through the `builder` container, via Castor:

```bash
castor builder -- bin/console cache:clear    # one-off command
castor builder -- bin/console make:migration # one-off command
```

Host prerequisites only: Docker, Bash, Castor.

## Essential commands

```bash
castor start                                 # build + install + up + migrate
castor stop                                  # stop the stack
castor logs [--service=service]              # logs (frontend, postgres, ...)
castor app:install                           # composer install + qa:install
castor app:cache-clear                       # clear (and warm up) the application cache
castor app:db:migrate                        # Doctrine migrations (alias: castor migrate)
castor postgres -- select 1 from foobar      # one-off command database query
```

Docker / worktrees:

```bash
castor docker:build [--service=service]
castor docker:up [--service=service]
castor docker:ports                          # ports allocated to the current worktree
```

## Castor contexts

The context changes how tasks are executed (`APP_ENV`, compose files, etc.):

```bash
castor --context=test qa:phpunit             # APP_ENV=test, for tests
castor --context=ci ...                      # like test, tuned for CI
castor --context=prod ...                    # production images on a dedicated local stack (docker-compose.prod.yml)
```

Always run tests and anything touching the database with `--context=test`.
Without option, the `default` context applies.

## Stack

- Symfony demo of the Workflow component, at the repository root (docroot = `public`).
  Unlike docker-starter's template, there is **no** `application/` directory: the app
  is also deployed on Clever Cloud (`clevercloud/`, `.buildpacks`, `app.json`), which
  builds from the repository root
- PostgreSQL 16: user/pass/db = `app`/`app`, DATABASE_URL already configured in `.env`
  (`app_test` database in the test environment)
- nginx + php-fpm (service `frontend`), Traefik router, HTTPS on `<root_domain>` (see `castor.php`)
- Node/yarn only inside the `builder` container
- Production images (`php` and `nginx`) can be built and tested locally from the
  "Production stages" of `infrastructure/docker/services/php/Dockerfile`; they are not
  published anywhere (the real deployment is Clever Cloud). php-fpm and nginx
  configuration (`services/php/php/`, `services/php/nginx/`) is shared with the dev
  `frontend` container

## QA — before considering a task done

Tools run inside the builder. `qa:cs` and `qa:twig-cs` **apply fixes by default** — pass `--dry-run` to only check.

```bash
castor qa:all                                # everything: cs + phpstan + twig-cs + phpunit
castor qa:cs [--dry-run]                     # PHP-CS-Fixer (.php-cs-fixer.php)
castor qa:phpstan [-b]                       # PHPStan level 8 (phpstan.neon)
castor qa:twig-cs [--dry-run]                # Twig-CS-Fixer
castor qa:phpunit                            # PHPUnit (tests/)
castor qa:security-audit                     # composer audit
```

After any PHP/Twig code change: `castor qa:cs --dry-run`,
`castor qa:phpstan`, then `castor --context=test qa:phpunit`.

## Conventions

1. **Never invoke `docker compose` by hand**: use the `docker_compose()` /
   `docker_compose_run()` functions from `.castor/docker.php` to write new tasks.
2. **Never hardcode ports or project names**: git worktree support automatically
   isolates project/volumes/ports. Use `variable('project_name')` etc.
3. New recurring task? Make it a Castor task (`castor.php` or `.castor/*.php`),
   not a shell script.
4. QA tool dependencies live in `tools/<tool>/composer.json`
   (not in the root `composer.json`).
