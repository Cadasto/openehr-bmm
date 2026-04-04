# AGENTS.md

This repository is a **GitHub template for PHP libraries** under the **Cadasto** organisation. Repos created from it are maintained by the organisation (us); they are public and open-source but development is done in-house. When you create or work on a library from this template, treat it as the same kind of project: a PHP library with the tooling and conventions described here.

## Purpose

- **Template**: Skeleton for new Cadasto PHP libraries (one example class + one test, Composer, CI, docs).
- **Not an application**: No runtime app entrypoint; the deliverable is a Composer-installable library.

## Maintainers

- **Cadasto** organisation. Libraries are developed by the organisation; there is no expectation of external maintainers, though the repos are public.

## Layout and ownership

| Area | Responsibility |
|------|----------------|
| `src/` | Library source; PSR-4 namespace as in `composer.json` |
| `tests/` | Unit/integration tests **and** tool config: `phpunit.xml`, `phpstan.neon`, `phpcs.xml`, `rector.php`, optional `phpstan-baseline.neon` |
| `docs/` | Project documentation. **Any documentation produced** (guides, architecture notes, setup, API notes, etc.) **should be placed in `docs/`**, not scattered in the repo root. |
| `.docker/` | Dockerfile and docker-compose.yml for the PHP 8.5 dev container; run from repo root with `-f .docker/docker-compose.yml` or use the Makefile. |
| `.github/workflows/` | CI (lint, CS, PHPStan, tests) and release on **version tags** (SemVer only, no `v` prefix, e.g. `1.0.0`; optional Packagist) |
| `README.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md` | Root-level team and process docs; keep in sync with this template. |

All coding standards and quality checks are defined by the config files in `tests/` (including `rector.php`) and the Composer scripts in `composer.json`.

## Standards (for contributors and agents)

- **Style**: PSR-12 (PHPCS; config in `tests/phpcs.xml`).
- **Static analysis**: PHPStan level 8 (`tests/phpstan.neon`).
- **Tests**: PHPUnit 12 (`tests/phpunit.xml`). Use `declare(strict_types=1);` and type hints.
- **Refactoring**: Rector (config in `tests/rector.php`). Run `composer rector` or `composer rector:dry-run` locally; not in CI by default.
- **Branching**: `main` is releasable; use feature/fix branches and run `composer ci` before opening a PR.
- **Commit messages**: Use conventional/GitHub style. **Keep the subject line short** (ideally under 72 characters) with a **type prefix** and imperative mood. Examples: `feat: add cache decorator`, `fix: handle empty input`, `chore: bump deps`, `docs: update setup guide`, `refactor: simplify validator`. Optionally add a blank line and a body for detail. Do **not** use long, sentence-like subjects without a prefix.

When editing this template or a repo created from it, keep config for PHPUnit, PHPStan, PHPCS, and Rector inside `tests/` so the library root stays minimal.

**PHP version**: The template uses PHP 8.5 in Docker for development; `composer.json` requires PHP `^8.4` for the library. CI and release workflows run on 8.4. If a library supports multiple PHP versions (e.g. 8.2 and 8.3), add a strategy matrix in the CI and release workflows and run the job for each version (e.g. `strategy.matrix.php: ['8.2', '8.3', '8.4']` with `shivammathur/setup-php`).

## Design and patterns (recommendations, not enforced)

Prefer established design patterns where they fit; do not enforce a single standard.

- **PSR interfaces**: When wrapping or extending behaviour (HTTP, logging, caching, etc.), consider implementing or depending on the relevant PSR (e.g. PSR-3 Logger, PSR-6/16 cache, PSR-7 HTTP messages) so the library composes well with the rest of the ecosystem.
- **Factory pattern**: Use when object creation is non-trivial or when you want to centralise construction (e.g. for test doubles or different implementations).
- **Decorator pattern**: Useful to add behaviour around a PSR or other interface without changing the original type (e.g. logging or caching decorators).
- **Dependency injection**: Prefer constructor injection and small, focused classes; avoid global state where possible.
- **Immutability**: Prefer immutable value objects and read-only interfaces where it simplifies reasoning and usage.

Choose patterns that suit the problem; consistency within a library is more important than following every pattern everywhere.

## Escalation and triage

1. Open or assign a GitHub issue with the `triage` label.
2. Mention the team or the Cadasto organisation as needed.
3. For release blockers or security: follow CONTRIBUTING.md and escalate within the organisation.

## How to get help

- **Usage or design**: GitHub Discussion or Issue.
- **Bug**: Use the bug report issue form.
- **Feature**: Use the feature request issue form.
- **Security**: Do **not** open a public issue; follow the security reporting instructions in `CONTRIBUTING.md`.

## Expectations

- **Triage**: Initial response within a few business days.
- **Critical regressions**: Same business day when possible.
- **Security reports**: Acknowledgement within 24 hours.
