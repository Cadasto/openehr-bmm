# AGENTS.md

This repository is an **opinionated PHP library** implementing the **openEHR Basic Meta-Model (BMM 2.4)** under the **Cadasto** organisation. It serves primarily as an intermediate representation (IR) of openEHR P_BMM specifications.

Use this file as the **primary reference** for agents, automation, and contribution expectations. See also **README.md** for install/commands and **CONTRIBUTING.md** for PR workflow.

## Purpose

- **Library**: PHP implementation of the openEHR BMM 2.4 specification, providing typed objects for schemas, packages, classes, properties, types, and functions parsed from P_BMM JSON.
- **Not an application**: No runtime entrypoint; the deliverable is a Composer-installable library.

## Maintainers

**Cadasto** organisation. Libraries are developed by the organisation; there is no expectation of external maintainers, though the repos are public.

## Layout and ownership

| Area | Responsibility |
|------|----------------|
| `src/` | Library source; PSR-4 namespace `Cadasto\OpenEHR\BMM\` per `composer.json` |
| `tests/` | Unit/integration tests **and** tool config: `phpunit.xml`, `phpstan.neon`, `phpcs.xml`, `rector.php`, optional `phpstan-baseline.neon` |
| `docs/` | Project documentation (guides, architecture, API notes). **Place new docs here**, not at the repo root except README/CONTRIBUTING/CODE_OF_CONDUCT/SECURITY. |
| `.claude/` | Claude Code project instructions (`CLAUDE.md`). |
| `.cursor/rules/` | Cursor rules (`project-context.mdc`, `commit-messages.mdc`, PHP/testing rules). |
| `.junie/` | JetBrains Junie guidelines (delegates to root `AGENTS.md`). |
| `.aiassistant/rules/` | JetBrains AI Assistant project rules. |
| `.docker/` | Dockerfile and docker-compose for the PHP 8.5 dev container; run from repo root via `-f .docker/docker-compose.yml` or the Makefile. |
| `.github/workflows/` | **CI** (CS, PHPStan, tests on PHP 8.4 and 8.5) and **release** on version tags (SemVer, no `v` prefix, e.g. `1.0.0`). Packagist updates via [GitHub webhook](docs/releases.md). |
| `README.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md` | Root-level process and team docs |

Coding standards and quality checks are defined by config files in `tests/` and the Composer scripts in `composer.json`.

> **Note:** `composer.lock` is not committed (library convention). CI runs `composer install` without a lock file.

## Documentation

### Domain (BMM / P_BMM)

- [docs/openehr-bmm-landscape.md](docs/openehr-bmm-landscape.md) — BMM vs P_BMM overview
- [docs/p-bmm-json-structure.md](docs/p-bmm-json-structure.md) — P_BMM JSON shape used by this IR

### Development

- [docs/development.md](docs/development.md) — development workflow and tooling details (**Composer / PHP run in Docker** — use `make ci`, `make install`, or `docker compose -f .docker/docker-compose.yml run --rm app composer …`)
- [docs/releases.md](docs/releases.md) — release process and tagging
- [docs/README.md](docs/README.md) — docs directory index

## Standards (for contributors and agents)

- **Style**: PSR-12 (PHPCS; config in `tests/phpcs.xml`).
- **Static analysis**: PHPStan level 8 (`tests/phpstan.neon`).
- **Tests**: PHPUnit 12 (`tests/phpunit.xml`). Use `declare(strict_types=1);` and type hints.
- **Refactoring**: Rector (config in `tests/rector.php`). Run **`composer rector`** inside the dev container (`make sh` or `docker compose … run --rm app composer rector`); Rector is not part of CI.
- **Branching**: `main` is releasable; use feature/fix branches and run **`make ci`** (or the equivalent Docker `composer ci`) before opening a PR.
- **Commit messages**: Conventional style — **`type: imperative subject`**, under ~72 characters, optional body after a blank line. Types: `feat`, `fix`, `chore`, `docs`, `refactor`, `test`. See [.cursor/rules/commit-messages.mdc](.cursor/rules/commit-messages.mdc) for full detail.

Keep PHPUnit, PHPStan, PHPCS, and Rector config under `tests/` so the library root stays minimal.

**PHP version**: Development uses PHP 8.5 in Docker; `composer.json` requires `^8.4`. CI runs the same checks on **PHP 8.4 and 8.5** (both must pass). The release workflow runs on PHP 8.4.

## IDE and agent integration

- **Claude Code**: Project instructions in **`.claude/CLAUDE.md`**; this file is the authoritative reference it points to.
- **Cursor**: Behavioural rules in **`.cursor/rules/`** (always-applied `project-context.mdc`, plus glob-attached PHP and testing rules).
- **JetBrains Junie**: Reads root **`AGENTS.md`**; **`.junie/guidelines.md`** delegates here.
- **JetBrains AI Assistant**: Project rules in **`.aiassistant/rules/`**.

## Model architecture

### Class hierarchy

All BMM model elements extend `AbstractBmmModel`, which implements `CollectableInterface` (for `Collection` storage) and `JsonSerializable` (delegating to `toArray()`). Shared defaults (`getAlias()` returning `null`, `jsonSerialize()` calling `toArray()`) live on `AbstractBmmModel` — do not duplicate them on concrete classes.

```
AbstractBmmModel  (getAlias, jsonSerialize → toArray)
  ├── AbstractBmmClass         → BmmClass, BmmInterface, BmmEnumerationString, BmmEnumerationInteger
  ├── AbstractBmmProperty      → BmmSingleProperty, BmmSinglePropertyOpen, BmmContainerProperty, BmmGenericProperty
  ├── AbstractBmmFunctionParameter → BmmSingleFunctionParameter, ..Open, BmmContainerFunctionParameter, BmmGenericFunctionParameter
  ├── AbstractBmmType          → BmmSimpleType, BmmContainerType, BmmGenericType
  ├── BmmSchema, BmmPackage, BmmFunction, BmmConstant, BmmGenericParameter, BmmSchemaInclude
```

The four intermediate abstracts (`AbstractBmmClass`, `AbstractBmmProperty`, `AbstractBmmFunctionParameter`, `AbstractBmmType`) exist **only** as polymorphic `fromArray()` dispatchers. Do not add shared behaviour to them — it belongs on `AbstractBmmModel`.

### Serialization

- **Models are format-neutral.** `toArray()` / `fromArray()` is the canonical exchange format. JSON encoding/decoding is handled by `Codec/JsonCodec`, not by the models. Do not add JSON-specific logic to model classes.
- **`array_filter()` stripping falsy values is intentional.** Fields like `is_abstract: false`, `ancestors: []`, and `invariants: []` are deliberately excluded from serialized output when they match their defaults. This is a design choice — do not change to null-only filtering.
- **`Helper/Interval`** represents cardinality constraint notation (`|0..*|`), not a BMM model element. It lives in `Helper/` alongside `Collection` and `CollectableInterface`. Do not move it to `Model/`.

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
