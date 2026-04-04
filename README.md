# PHP Library Template (Cadasto)

A **GitHub template repository** for creating PHP 8.4+ libraries under the Cadasto namespace. It provides a minimal skeleton (one example class and one test) and a standard toolchain so new libraries can be created quickly and consistently.

## Use this template

1. On GitHub: **Use this template** → **Create a new repository**.
2. Clone your new repo, then replace placeholders (see below).
3. Run the checks and start developing.

This is not a runnable application—it is a starting point for **libraries** that other projects will depend on via Composer.

## What’s included

- **Skeleton**: One class (`src/Greeter.php`) and one PHPUnit test (`tests/TestCase/GreeterTest.php`) to verify the setup.
- **Composer**: PHP `^8.4`, PSR-4 autoload for `src/` and `tests/`, and dev tools (PHPUnit, PHPStan, PHPCS, parallel-lint).
- **Config in `tests/`**: `tests/phpunit.xml`, `tests/phpstan.neon`, `tests/phpcs.xml` (and optional `tests/phpstan-baseline.neon`) keep tooling config out of the library root.
- **Docker**: `.docker/Dockerfile` and `.docker/docker-compose.yml` for a consistent PHP 8.5 dev environment (library still targets PHP 8.4+ in `composer.json`).
- **CI/CD**: GitHub Actions for pull requests and pushes to `main` (lint, CS, PHPStan, tests), and a release workflow on version tags.
- **Refactoring**: Rector (dev only; run `composer rector` or `composer rector:dry-run`; not in CI by default).
- **Docs and process**: Issue templates (bug report, feature request, question/other), PR template, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`, and `AGENTS.md` for the team and AI agents.
- **Automation**: Dependabot config for Composer and GitHub Actions updates.

## Default placeholders

After creating a repository from this template, replace:

| Placeholder | Where |
|-------------|--------|
| Vendor / package name | `composer.json`: `name`, `description`; Packagist |
| Namespace `Cadasto\TemplateRepo` | `composer.json` autoload; all classes in `src/` and test namespaces in `tests/` |
| “Cadasto Template Repo” / “template-repo” | README, docs, workflow descriptions, and any badges |

### Template checklist (after using this template)

- [ ] Replace the placeholders above (name, namespace, descriptions).
- [ ] Set the security contact in **SECURITY.md** (e.g. `security@cadasto.com`).
- [ ] Set conduct/security contacts in **CONTRIBUTING.md** and **CODE_OF_CONDUCT.md** if your organisation uses custom ones.
- [ ] Enable **branch protection** for `main` (e.g. require PR reviews, require status checks).
- [ ] If publishing to Packagist: submit the package, add repository secrets for the release workflow, and set `keywords` in `composer.json` for discoverability.
- [ ] Optionally add README badges (CI status, Packagist version, license).

## Requirements

- **Local**: PHP 8.4+ (matches `composer.json`), Composer 2.7+
- **Docker**: PHP 8.5 in the image; Docker Engine and Docker Compose (plugin)

## Quick start

### With Docker

Docker files are in `.docker/`. From repo root, use the Makefile (recommended) or pass the compose file explicitly:

```bash
make build
make install
make ci
```

Or with `docker compose` directly (from repo root):

```bash
docker compose -f .docker/docker-compose.yml build
docker compose -f .docker/docker-compose.yml run --rm app composer install
docker compose -f .docker/docker-compose.yml run --rm app composer ci
```

(See Makefile for other targets: `up`, `down`, `sh`, `env`, etc.)

### Without Docker

```bash
composer install
composer ci
```

## Repository layout

| Area | Location |
|------|----------|
| Library source | `src/` |
| Tests and tool config | `tests/` (PHPUnit, PHPStan, PHPCS, Rector) |
| Project documentation | `docs/` |
| Docker | `.docker/` (Dockerfile, docker-compose.yml) |
| CI / release | `.github/workflows/` |
| Issue forms / PR template | `.github/ISSUE_TEMPLATE/`, `.github/PULL_REQUEST_TEMPLATE.md` |
| Security / automation | `SECURITY.md`, `.github/dependabot.yml` |
| Root-level docs | `README.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `AGENTS.md` |

## Composer scripts

Main commands: `composer install`, `composer ci`, `composer test`, `composer rector`. Full list → [docs/development.md](docs/development.md).

## Standards and tooling

PSR-12 (PHPCS), PHPStan 8, PHPUnit 12, Rector (local only). Details → [docs/development.md](docs/development.md#standards-and-tooling).

## Releases

Tag with SemVer (no `v` prefix), e.g. `git tag 1.0.0 && git push origin 1.0.0`. Release workflow runs CI, creates a GitHub Release, and can trigger Packagist. Full steps and Packagist setup → [docs/releases.md](docs/releases.md).

## License

MIT — see [LICENSE](LICENSE).
