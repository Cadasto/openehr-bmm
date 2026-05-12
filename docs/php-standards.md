# PHP standards

Canonical language and style rules. Also referenced from
[.cursor/rules/php-standards.mdc](../.cursor/rules/php-standards.mdc).

## Language

- **PHP version**: `^8.4` (declared in `composer.json`). Dev container runs
  PHP 8.5; CI runs both 8.4 and 8.5 and both must pass.
- **`declare(strict_types=1);`** at the top of every PHP file — no exceptions.

## Style

- **PSR-12**, enforced by PHPCS. Config: `tests/phpcs.xml`.
- **Short array syntax** `[]`.
- **PascalCase** for class names; **camelCase** for methods and properties;
  **snake_case** for serialized array keys (matches P_BMM JSON).
- **Type hints**: parameters, return types, and properties all typed. Use
  union types and intersections where they clarify intent. `readonly`
  classes and properties for value-object models.

## Static analysis

- **PHPStan level 8** (config: `tests/phpstan.neon`). The repo carries an
  optional `tests/phpstan-baseline.neon`; new code must not add to it.

## Namespaces

| Code | Namespace prefix |
|------|------------------|
| Library source (`src/`) | `Cadasto\OpenEHR\BMM\` |
| Tests (`tests/`) | `Tests\` |

Both are wired in `composer.json` under `autoload` / `autoload-dev`.

## Running PHP locally

**PHP, Composer, and `vendor/bin/*` run inside the dev container.** Do not
assume `composer` or `php` exist on the host PATH. From the repo root:

- `make install` — `composer install` in the container
- `make ci` — full CI sweep (lint, CS, PHPStan, tests)
- `make sh` — interactive shell
- `docker compose -f .docker/docker-compose.yml run --rm app composer <script>`
  for any other Composer script

Full command reference: [testing.md](testing.md).

## Refactoring

Rector is configured (`tests/rector.php`) but **not** part of CI. Run
`composer rector` or `composer rector:dry-run` inside the container as
a manual cleanup pass.
