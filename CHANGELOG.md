# Changelog

All notable changes to this project should be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `BmmIndexedContainerProperty` and `BmmIndexedContainerType` for `P_BMM_INDEXED_CONTAINER_PROPERTY` / `P_BMM_INDEXED_CONTAINER_TYPE` (e.g. `Hash<String, EVENT_ACTION>`). Before, the property was parsed as a `BmmSingleProperty` of type `Any` and the type as a `BmmSimpleType`, losing the container and index types. They extend `BmmContainerProperty` and `BmmContainerType`.
- `BmmOpenType` for `P_BMM_OPEN_TYPE` (e.g. `T`). Before, it was parsed as `BmmSimpleType` and written back as `P_BMM_SIMPLE_TYPE`. It extends `BmmSimpleType`, so existing `instanceof BmmSimpleType` checks still match.
- `BmmClass::$ancestorDefs` for generic inheritance (`ancestor_defs`), read as a list or as a map keyed by type signature, and `BmmClass::$isOverride` (`is_override`); before, both were dropped.
- `BmmSchema` header attributes `modelName`, `schemaContributors` and the legacy `archetypeParentClass`, `archetypeDataValueParentClass`, `archetypeRmClosurePackages` and `archetypeVisualiseDescendantsOf`; before, they were dropped. All new constructor parameters are optional and come last.

### Changed

- PHPUnit **13** (`^13.4`, was `^12.5`); `tests/phpunit.xml` uses `resolveDependencies="true"` in place of the deprecated `executionOrder="depends"`.
- `composer check:phpstan-baseline` is renamed to `composer phpstan:baseline`, because it writes the baseline file and is not a check.
- `.claude/CLAUDE.md` imports `AGENTS.md`; before, Claude Code skipped `AGENTS.md` because a `CLAUDE.md` existed, and the file's relative links were broken.
- Docs aligned with the P_BMM 2.4 specification: `bmm_version` is the P_BMM version, not a tool format version; BMM is STABLE and BMM 3 is PAUSED; `docs/architecture.md` lists the library's P_BMM 2.4 coverage and the deviations from the class model it reads in published schemas.
- `ext-xdebug` is no longer in `require-dev`, so `composer install` works without Xdebug and without `--ignore-platform-req`. `composer test:coverage` still needs Xdebug; the dev image includes it.

### Fixed

- The PHPStan baseline script called a non-existent `@phpstan` script; it now runs `check:phpstan` and can write an empty baseline.

## [0.3.0]

### Added

- `Collection::populateFrom($items, $factory, bool $keyed = false)` helper.

### Fixed

- `BmmGenericType::toArray()` emits plain arrays inside `generic_parameters` (no more live `AbstractBmmType` instances).
- `BmmSingleFunctionParameterOpen::fromArray()` defaults `type` to `'Any'`, matching its sibling.
- `BmmSchema::fromArray()` raises `InvalidArgumentException` naming the missing field instead of warning + `TypeError`.

### Changed

- `BmmSchema::toArray()` emits `schema_description` verbatim; no fallback to `schemaName`.
- Every model `fromArray()` uses `Collection::populateFrom()`; `BmmFunction` parameters insert via `Collection::set()`.
- Docs reorganised into topical canonical files under `docs/`; `AGENTS.md`, `.cursor/rules/*.mdc`, and `.claude/CLAUDE.md` slimmed to delegate.
- CI: `softprops/action-gh-release` bumped from v2 to v3.

## [0.2.1]

### Changed

- CI: PHP **8.4** and **8.5** matrix; `actions/checkout` and `actions/cache` **v5**.
- Packagist updates **webhooks only**. 

## [0.2.0]

### Added

- BMM 2.4 model classes: `BmmSchema`, `BmmClass`, `BmmInterface`, `BmmPackage`, `BmmConstant`, `BmmFunction`
- Enumeration types: `BmmEnumerationString`, `BmmEnumerationInteger`
- Property types: `BmmSingleProperty`, `BmmSinglePropertyOpen`, `BmmContainerProperty`, `BmmGenericProperty`
- Type system: `BmmSimpleType`, `BmmContainerType`, `BmmGenericType`, `BmmGenericParameter`
- Function parameters: `BmmSingleFunctionParameter`, `BmmSingleFunctionParameterOpen`, `BmmContainerFunctionParameter`, `BmmGenericFunctionParameter`
- Schema includes: `BmmSchemaInclude`
- Base class `AbstractBmmModel` with shared `getAlias()` and `jsonSerialize()` defaults
- Polymorphic `fromArray()` dispatchers on `AbstractBmmClass`, `AbstractBmmProperty`, `AbstractBmmFunctionParameter`, `AbstractBmmType`
- Format-neutral serialization: `toArray()` / `fromArray()` on all model classes (via `CollectableInterface`)
- Codec layer: `BmmCodecInterface` and `JsonCodec` for JSON encoding/decoding, separate from model logic
- Helper classes: `Collection`, `CollectableInterface`, `Interval` (cardinality constraint value object)
- Test resources: `openehr_base_1.3.0.bmm.json`, `openehr_rm_1.2.0.bmm.json`

## [0.1.0] - Template

- Initial template: skeleton, Composer tooling, Docker, GitHub Actions CI and release, issue templates, and docs.
