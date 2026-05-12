# Architecture

Canonical model architecture, invariants, "do not touch" areas, and
migration rules.

## Class hierarchy

All BMM model elements extend `AbstractBmmModel`, which implements
`CollectableInterface` (for `Collection` storage) and `JsonSerializable`
(delegating to `toArray()`).

```
AbstractBmmModel  (getAlias, jsonSerialize → toArray)
  ├── AbstractBmmClass              → BmmClass, BmmInterface,
  │                                   BmmEnumerationString, BmmEnumerationInteger
  ├── AbstractBmmProperty           → BmmSingleProperty, BmmSinglePropertyOpen,
  │                                   BmmContainerProperty, BmmGenericProperty
  ├── AbstractBmmFunctionParameter  → BmmSingleFunctionParameter,
  │                                   BmmSingleFunctionParameterOpen,
  │                                   BmmContainerFunctionParameter,
  │                                   BmmGenericFunctionParameter
  ├── AbstractBmmType               → BmmSimpleType, BmmContainerType,
  │                                   BmmGenericType
  └── BmmSchema, BmmPackage, BmmFunction, BmmConstant,
      BmmGenericParameter, BmmSchemaInclude
```

## Invariants

1. **Shared model behaviour lives on `AbstractBmmModel`.** `getAlias()`
   returning `null` and `jsonSerialize()` calling `toArray()` are defined
   once on the base class. Do not duplicate them on concrete classes.

2. **The four intermediate abstracts are dispatchers, not base classes.**
   `AbstractBmmClass`, `AbstractBmmProperty`, `AbstractBmmFunctionParameter`,
   and `AbstractBmmType` exist *only* to host polymorphic `fromArray()` that
   reads `_type` and routes to the right concrete subclass. Do not add
   shared behaviour to them — that belongs on `AbstractBmmModel`.

3. **Models are format-neutral.** `toArray()` / `fromArray()` is the
   canonical exchange format. JSON encoding/decoding is handled by
   `Codec/JsonCodec`, not by the models. Do not add JSON-specific logic to
   model classes.

4. **`array_filter()` stripping falsy values is intentional.** Fields like
   `is_abstract: false`, `ancestors: []`, and `invariants: []` are
   deliberately excluded from serialized output when they match their
   defaults. This is a design choice — do not change to null-only filtering.

5. **`Helper/Interval` is a helper, not a model.** It represents cardinality
   constraint notation (`|0..*|`), not a BMM model element. It lives in
   `Helper/` alongside `Collection` and `CollectableInterface`. Do not move
   it to `Model/`.

6. **Collection insertion idiom.** Use `Collection::add()` when the key is
   the item's name (the common case) and `Collection::set($key, $item)`
   when the P_BMM document supplies the key explicitly (e.g. generic
   formal parameter names `T`, `K`, `V` or function parameter names).
   `Collection::offsetSet()` is an internal `ArrayObject` primitive — do
   not call it from model code; use `add` or `set`.

7. **`Collection::populateFrom()` is the standard fromArray idiom.**
   Iterating `$data['xxx']` with `array_walk` and an `add`/`set` closure is
   superseded — pass the raw array and a factory to `populateFrom()` so
   the empty/non-iterable guard is uniform.

## Do not touch

- `tests/phpunit.xml`, `tests/phpstan.neon`, `tests/phpcs.xml`,
  `tests/rector.php`: tool config stays under `tests/`, not at repo root.
- `Helper/Interval` does not move into `Model/` (see invariant 5).
- `composer.lock` is not committed (library convention; CI runs
  `composer install` without a lock file).

## Migration rules

The library follows **SemVer** (no `v` prefix; see [releases.md](releases.md)).

The fromArray/toArray contract is the canonical invariant:

- `BmmSchema::fromArray($data)->toArray()` must round-trip cleanly for any
  valid P_BMM JSON payload. Field defaults stripped by `array_filter` are
  the only intentional omissions (see invariant 4).
- Breaking changes to that contract — renaming JSON fields, changing
  `_type` discriminators, adding required fields without defaults, or
  removing dispatcher cases — require a **major** version bump.
- Adding new optional fields, new `_type` cases handled by a new
  concrete subclass, or new helper methods are **minor** bumps.
- Bug fixes that restore the documented round-trip behaviour are **patch**
  bumps.

When the `BmmSchema::BMM_VERSION` constant changes (current `2.4`), update
test fixtures under `tests/resources/` and document the format delta in
[p-bmm-json-structure.md](p-bmm-json-structure.md).
