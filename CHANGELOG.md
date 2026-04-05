# Changelog

All notable changes to this project should be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
