# Testing and CI

Canonical reference for running tests, lint, static analysis, and the full
CI sweep. Also referenced from
[.cursor/rules/testing-and-ci.mdc](../.cursor/rules/testing-and-ci.mdc).

## Container-only execution

**PHP, Composer, and `vendor/bin/*` run inside the dev container** — not on
the host. Do **not** assume `composer` or `php` exist on the host PATH.

From the repository root:

| Command | Purpose |
|---------|---------|
| `make install` | `composer install` in the container |
| `make ci` | Full CI: lint, PHPCS, PHPStan, PHPUnit |
| `make sh` | Interactive shell in the container |
| `docker compose -f .docker/docker-compose.yml run --rm app composer <script>` | Run any Composer script |

## Composer scripts

Run via `make ci`, `make install`, or the `docker compose … composer …`
form above.

| Script | Description |
|--------|-------------|
| `composer test` | PHPUnit |
| `composer test:dox` | PHPUnit with testdox output |
| `composer test:coverage` | PHPUnit with HTML coverage report in `var/` |
| `composer check:lint` | parallel-lint (syntax) |
| `composer check:cs` | PHPCS (PSR-12) |
| `composer check:phpstan` | PHPStan level 8 |
| `composer check:phpstan-baseline` | Generate / refresh `tests/phpstan-baseline.neon` |
| `composer rector` | Run Rector (applies changes; not in CI) |
| `composer rector:dry-run` | Run Rector in dry-run mode |
| `composer ci` | Run lint, CS, PHPStan, and tests (what GitHub Actions runs) |

## Configuration locations

All tool config lives under `tests/`:

- `tests/phpunit.xml`
- `tests/phpstan.neon` (+ optional `tests/phpstan-baseline.neon`)
- `tests/phpcs.xml`
- `tests/rector.php`

Do **not** move these to the repository root.

## Filtering PHPUnit

```bash
docker compose -f .docker/docker-compose.yml run --rm app \
  composer test -- --filter CollectionTest
```

The `--` separates Composer-script args from the PHPUnit args that follow.

## Before opening a PR

1. Rebase on `main`.
2. Run `make ci` (or the equivalent `docker compose … composer ci`).
3. All four checks (lint, CS, PHPStan level 8, PHPUnit) must pass on **both
   PHP 8.4 and 8.5** — CI runs both matrices.

## CI

- **CI workflow** (`.github/workflows/ci.yml`): runs `composer ci` on
  PHP 8.4 and 8.5 for every PR and push to `main`.
- **Release workflow**: runs on SemVer tags only — see
  [releases.md](releases.md).
