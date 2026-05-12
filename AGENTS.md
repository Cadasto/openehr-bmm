# AGENTS.md

This repository is an **opinionated PHP library** implementing the **openEHR Basic Meta-Model (BMM 2.4)** under the **Cadasto** organisation. It serves primarily as an intermediate representation (IR) of openEHR P_BMM specifications.

Authoritative agent and contributor guidance lives in [`docs/`](docs/) — this file is the human-facing entry point and process reference.

## Purpose

- **Library**: PHP implementation of openEHR BMM 2.4, providing typed objects for schemas, packages, classes, properties, types, and functions parsed from P_BMM JSON.
- **Not an application**: no runtime entrypoint; the deliverable is a Composer-installable library.

## Where to look

| Topic | Document |
|-------|----------|
| Package layout, maintainership & where to find help | [docs/project-context.md](docs/project-context.md) |
| Model class hierarchy, invariants, "do not touch", migration rules | [docs/architecture.md](docs/architecture.md) |
| PHP version, PSR-12, namespaces, naming | [docs/php-standards.md](docs/php-standards.md) |
| Composer scripts, container execution, CI, before-PR | [docs/testing.md](docs/testing.md) |
| Commit message format | [docs/commit-style.md](docs/commit-style.md) |
| Design pattern recommendations (not enforced) | [docs/design-patterns.md](docs/design-patterns.md) |
| Release tags & Packagist | [docs/releases.md](docs/releases.md) |
| BMM / P_BMM domain background | [docs/openehr-bmm-landscape.md](docs/openehr-bmm-landscape.md), [docs/p-bmm-json-structure.md](docs/p-bmm-json-structure.md) |

Root-level process docs that stay at the root: [`README.md`](README.md) (install + quick-start), [`CONTRIBUTING.md`](CONTRIBUTING.md), [`CODE_OF_CONDUCT.md`](CODE_OF_CONDUCT.md), [`SECURITY.md`](SECURITY.md), [`CHANGELOG.md`](CHANGELOG.md).

## IDE and agent integration

All editor / agent rule files **delegate** to the documents above:

- **Claude Code**: [`.claude/CLAUDE.md`](.claude/CLAUDE.md).
- **Cursor**: [`.cursor/rules/`](.cursor/rules/) (always-applied `project-context.mdc` and `commit-messages.mdc`, plus glob-attached `php-standards.mdc` and `testing-and-ci.mdc`).
- **JetBrains Junie**: [`.junie/guidelines.md`](.junie/guidelines.md).
- **JetBrains AI Assistant**: [`.aiassistant/rules/general.md`](.aiassistant/rules/general.md).
- **GitHub Copilot**: [`.github/copilot-instructions.md`](.github/copilot-instructions.md).

## Escalation and triage

1. Open or assign a GitHub issue with the `triage` label.
2. Mention the team or the Cadasto organisation as needed.
3. For release blockers or security: follow [`CONTRIBUTING.md`](CONTRIBUTING.md) and escalate within the organisation.

## How to get help

- **Usage or design**: GitHub Discussion or Issue.
- **Bug**: Use the bug report issue form.
- **Feature**: Use the feature request issue form.
- **Security**: Do **not** open a public issue; follow the security reporting instructions in [`SECURITY.md`](SECURITY.md).

## Expectations

- **Triage**: Initial response within a few business days.
- **Critical regressions**: Same business day when possible.
- **Security reports**: Acknowledgement within 24 hours.
