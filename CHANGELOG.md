# Changelog

All notable changes to this project should be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
