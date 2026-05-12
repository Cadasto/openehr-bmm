# Project context

Canonical short-form summary for AI agents and contributors. Also
referenced from [.cursor/rules/project-context.mdc](../.cursor/rules/project-context.mdc).

## What this is

`cadasto/openehr-bmm` — opinionated PHP library implementing the openEHR
**Basic Meta-Model (BMM 2.4)**, mostly as an intermediate representation of
**P_BMM** persistence-format documents. Composer-installable **library**, not
an application; no runtime entrypoint.

- **Spec context**: see [openehr-bmm-landscape.md](openehr-bmm-landscape.md)
  for BMM vs P_BMM and [p-bmm-json-structure.md](p-bmm-json-structure.md) for
  the JSON shape this IR maps to.
- **Package**: `Cadasto\OpenEHR\BMM\*` (PSR-4 from `composer.json`).

## Package layout

| Path | Purpose |
|------|---------|
| `src/` | Library source (PSR-4 `Cadasto\OpenEHR\BMM\`). |
| `src/Model/` | BMM model classes — schemas, packages, classes, properties, types, functions, enumerations. |
| `src/Helper/` | `Collection`, `CollectableInterface`, `Interval`. **Not** model elements. |
| `src/Codec/` | Format codecs (`BmmCodecInterface`, `JsonCodec`). |
| `tests/` | PHPUnit tests **and** tool config: `phpunit.xml`, `phpstan.neon`, `phpcs.xml`, `rector.php`, optional `phpstan-baseline.neon`. |
| `tests/resources/` | BMM JSON fixtures (`openehr_base_1.3.0.bmm.json`, `openehr_rm_1.2.0.bmm.json`). |
| `docs/` | This documentation tree. **Place new docs here**, not at the repo root. |
| `.docker/` | Dev container (PHP 8.5). Compose file lives here; run from repo root via `-f .docker/docker-compose.yml`. |
| `.github/workflows/` | CI on PHP 8.4 + 8.5; release workflow on SemVer tags. |
| `.cursor/`, `.junie/`, `.aiassistant/`, `.claude/`, `.github/copilot-instructions.md` | Editor / agent rule files. All delegate to this docs tree. |

Root-level docs that stay at the root: `README.md`, `AGENTS.md`,
`CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`, `CHANGELOG.md`,
`LICENSE`.

## Topical references

| Topic | Document |
|-------|----------|
| Class hierarchy, invariants, "do not touch", migration | [architecture.md](architecture.md) |
| PHP version, PSR-12, namespace, naming | [php-standards.md](php-standards.md) |
| Composer scripts, container exec, CI | [testing.md](testing.md) |
| Commit message format | [commit-style.md](commit-style.md) |
| Design pattern recommendations (not enforced) | [design-patterns.md](design-patterns.md) |
| Release tags & Packagist | [releases.md](releases.md) |

## Maintainership and escalation

Maintained by the **Cadasto** organisation. No expectation of external
maintainers; repos are public. For triage / escalation / security, see
[AGENTS.md](../AGENTS.md) at the repo root.
