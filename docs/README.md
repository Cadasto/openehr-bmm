# Documentation index

Topical documentation for the **openEHR BMM** PHP library. Each file is
self-contained; Cursor / Junie / Claude / AI Assistant / Copilot rules
delegate here.

## Project rules (canonical for AI and humans)

| Document | What it covers |
|----------|----------------|
| [project-context.md](project-context.md) | What the library is, package layout, where to find help |
| [architecture.md](architecture.md) | Model class hierarchy, invariants, "do not touch", migration rules |
| [php-standards.md](php-standards.md) | PHP 8.4, `strict_types`, PSR-12, namespaces, naming |
| [testing.md](testing.md) | Composer scripts, container execution, CI, before-PR checklist |
| [commit-style.md](commit-style.md) | Conventional commit format with examples |
| [design-patterns.md](design-patterns.md) | Pattern recommendations (PSR interfaces, factory, decorator, DI, immutability) — not enforced |
| [releases.md](releases.md) | SemVer tags, release workflow, Packagist |

## Domain background

| Document | What it covers |
|----------|----------------|
| [openehr-bmm-landscape.md](openehr-bmm-landscape.md) | BMM vs P_BMM specifications, version lines, how this library fits |
| [p-bmm-json-structure.md](p-bmm-json-structure.md) | Inferred P_BMM JSON shape (format 2.4), `_type` discriminators, schema addressing |

## Root-level docs (outside this directory)

- [`README.md`](../README.md) — install and quick-start
- [`AGENTS.md`](../AGENTS.md) — slim human-facing entry point; delegates here
- `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`, `CHANGELOG.md`
