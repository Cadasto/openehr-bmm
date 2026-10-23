# Changelog

All notable changes to this project should be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `BmmIndexedContainerProperty` and `BmmIndexedContainerType` for `P_BMM_INDEXED_CONTAINER_PROPERTY` / `P_BMM_INDEXED_CONTAINER_TYPE` (e.g. `Hash<String, EVENT_ACTION>`). Before, the property was parsed as a `BmmSingleProperty` of type `Any` and the type as a `BmmSimpleType`, losing the container and index types. They extend `BmmContainerProperty` and `BmmContainerType`; `BmmIndexedContainerProperty::getIndexedTypeDef()` returns the type with its `indexType`, which the inherited `$typeDef` (declared as `BmmContainerType`) does not expose to static analysis.
- `BmmOpenType` for `P_BMM_OPEN_TYPE` (e.g. `T`). Before, it was parsed as `BmmSimpleType` and written back as `P_BMM_SIMPLE_TYPE`. It extends `BmmSimpleType`, so existing `instanceof BmmSimpleType` checks still match.
- `BmmClass::$ancestorDefs` for generic inheritance (`ancestor_defs`), read as a list or as a map keyed by type signature, with non-generic entries kept as their own type, and `$isOverride` (`is_override`) on `BmmClass`, `BmmEnumerationString` and `BmmEnumerationInteger`; before, both were dropped.
- `BmmSchema` header attributes `modelName`, `schemaContributors` and the legacy `archetypeParentClass`, `archetypeDataValueParentClass`, `archetypeRmClosurePackages` and `archetypeVisualiseDescendantsOf` (also read from Archie's spelling `archetype_visualize_descendants_of`); before, they were dropped. All new constructor parameters are optional and come last.

### Changed

- PHPUnit **13** (`^13.4`, was `^12.5`); `tests/phpunit.xml` uses `resolveDependencies="true"` in place of the deprecated `executionOrder="depends"`.
- `composer check:phpstan-baseline` is renamed to `composer phpstan:baseline`, because it writes the baseline file and is not a check.
- `.claude/CLAUDE.md` imports `AGENTS.md`; before, Claude Code skipped `AGENTS.md` because a `CLAUDE.md` existed, and the file's relative links were broken.
- Docs aligned with the P_BMM 2.4 specification: `bmm_version` is the P_BMM version, not a tool format version; BMM is STABLE and BMM 3 is PAUSED; `docs/architecture.md` lists the library's P_BMM 2.4 coverage and the deviations from the class model it reads in published schemas.
- `ext-xdebug` is no longer in `require-dev`, so `composer install` works without Xdebug and without `--ignore-platform-req`. `composer test:coverage` still needs Xdebug; the dev image includes it.
- The `Interval` constructor's `upperUnbounded` parameter now defaults to `null` (was `true`): when it is not given, it follows whether `upper` is set. `lowerUnbounded` is always `false` after construction.

### Fixed

- The PHPStan baseline script called a non-existent `@phpstan` script; it now runs `check:phpstan` and can write an empty baseline.
- `Interval` keeps a bounded interval bounded: an `upper` value without `upper_unbounded` now gives `upperUnbounded === false`, so a `|2..4|` cardinality no longer turns into `|>=2|`, whether it comes from `fromArray()` or from `new Interval(lower: 2, upper: 4)` ([#8](https://github.com/Cadasto/openehr-bmm/issues/8)).
- `Interval` no longer writes a contradictory cardinality back out. `upper_unbounded: true` drops a stray `upper`, and a missing `upper` reads as open above even when `upper_unbounded` is `false`. `lower_unbounded: true` and a negative `lower` read as `lower: 0`, because a count is never below 0. An excluded bound (`lower_included` or `upper_included: false`) becomes the included one next to it, so `0..<5` reads as `0..4`. A cardinality that admits no count, its upper bound below its lower bound (`6..4`, or `0..<0`), throws `InvalidArgumentException` instead of being written back inverted.

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
