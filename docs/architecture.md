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
  │                                   BmmContainerProperty (→ BmmIndexedContainerProperty),
  │                                   BmmGenericProperty
  ├── AbstractBmmFunctionParameter  → BmmSingleFunctionParameter,
  │                                   BmmSingleFunctionParameterOpen,
  │                                   BmmContainerFunctionParameter,
  │                                   BmmGenericFunctionParameter
  ├── AbstractBmmType               → BmmSimpleType (→ BmmOpenType),
  │                                   BmmContainerType (→ BmmIndexedContainerType),
  │                                   BmmGenericType
  └── BmmSchema, BmmPackage, BmmFunction, BmmConstant,
      BmmGenericParameter, BmmSchemaInclude
```

The three classes in parentheses extend a concrete class; see
[P_BMM 2.4 coverage](#p_bmm-24-coverage) for why.

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

   It reads every cardinality as a multiplicity, and its constructor
   normalises the input, so `fromArray()` and `new Interval(...)` agree:
   - no `upper` means open above; an `upper` without `upper_unbounded` is
     bounded, and `upper_unbounded: true` drops a stray `upper`;
   - the lower bound is a count, so a missing or negative `lower`, or
     `lower_unbounded: true`, reads as 0;
   - both bounds are included; an excluded bound
     (`*_included: false`) becomes the included one next to it;
   - an interval that admits no count, its upper bound below its lower
     bound (`6..4`, or `0..<0` once the excluded bound is converted),
     throws `InvalidArgumentException`.

   `toArray()` then writes `lower` plus `upper` or `upper_unbounded: true`,
   the shape every published P_BMM file uses. The openEHR specifications
   leave this encoding unstated; the evidence for each rule is collected in
   [#8](https://github.com/Cadasto/openehr-bmm/issues/8) and its pull request.

6. **Collection insertion idiom.** Use `Collection::add()` when the key is
   the item's name (the common case) and `Collection::set($key, $item)`
   when the P_BMM document supplies the key explicitly (e.g. generic
   formal parameter names `T`, `K`, `V` or function parameter names).
   `Collection::offsetSet()` is an internal `ArrayObject` primitive — do
   not call it from model code; use `add` or `set`.

7. **`Collection::populateFrom()` is the standard fromArray idiom.**
   Iterating `$data['xxx']` with `array_walk` and an `add`/`set` closure is
   superseded — pass the raw array and a factory to `populateFrom()` so
   the empty/non-iterable guard is uniform. Models are `readonly` classes,
   but their `Collection` properties are mutable `ArrayObject`s: `fromArray()`
   constructs the instance with empty collections, then fills them. Never
   reassign a collection property.

8. **Unknown `_type` values are not rejected.** Each dispatcher sends a
   missing or unrecognised `_type` to its default class (`BmmClass`,
   `BmmSingleProperty`, `BmmSingleFunctionParameter`, `BmmSimpleType`), so a
   P_BMM variant the library does not model would be parsed as the default
   without an error. Every concrete P_BMM 2.4 discriminator is modelled
   today; see [P_BMM 2.4 coverage](#p_bmm-24-coverage).

## P_BMM 2.4 coverage

Checked against the
[P_BMM specification](https://specifications.openehr.org/releases/LANG/development/bmm_persistence.html)
(STABLE, 2.4) and the published openEHR schemas in
[specifications-ITS-BMM](https://github.com/openEHR/specifications-ITS-BMM).

**Modelled.** Every concrete persisted `P_BMM_*` type, including indexed
containers (`BmmIndexedContainerProperty`, `BmmIndexedContainerType`) and open
types (`BmmOpenType`); class `ancestor_defs`; `is_override` on classes and
enumerations (`P_BMM_INTERFACE` does not inherit it); and the optional schema
header attributes `model_name` and `schema_contributors`.

**Outside the class model.** The spec's header example uses `model_name`, the
archetype RM closure name (`EHR` in `openEHR-EHR-...`), but no class declares
it; `is_override` is declared on `P_BMM_CLASS` but not marked as persisted.
[Archie](https://github.com/openEHR/archie) reads and writes both, so the
library does too.

**Legacy header attributes.** `archetype_parent_class`,
`archetype_data_value_parent_class`, `archetype_rm_closure_packages` and
`archetype_visualise_descendants_of` were removed from P_BMM in 2018 and now
live in archetype profiles. `BMM_SCHEMA_CORE` still declares them as
persisted, and older schemas carry them, so the library keeps them for a
faithful round trip; Archie reads them as deprecated.

**Backward compatibility.** Before these were modelled, each was parsed by a
fallback class. The new classes extend that class, so existing
`instanceof` branches keep matching:

| Class | Extends | Note |
|-------|---------|------|
| `BmmIndexedContainerProperty` | `BmmContainerProperty` | Matches the spec (`P_BMM_INDEXED_CONTAINER_PROPERTY` → `P_BMM_CONTAINER_PROPERTY`). The inherited `$typeDef` stays declared as `BmmContainerType`, since PHP cannot narrow it; `getIndexedTypeDef()` returns it as `BmmIndexedContainerType`. |
| `BmmIndexedContainerType` | `BmmContainerType` | Matches the spec. |
| `BmmOpenType` | `BmmSimpleType` | A deliberate departure: in P_BMM the two are siblings. Test for `BmmOpenType` first to tell them apart. |

**Tolerated deviations in published schemas.** These inputs differ from the
P_BMM class model; the library reads them and writes the form shown.

| Input | Output |
|-------|--------|
| `class_definitions` / `primitive_types` as a list (the class model's `List<P_BMM_CLASS>`) | A map keyed by class name, as in the spec text and every published schema. |
| `ancestor_defs` as a list (class model) or a map keyed by type signature (spec example) | The same shape as the input. |
| A non-generic `ancestor_defs` entry, such as a `P_BMM_SIMPLE_TYPE` (Archie uses `ancestor_defs` in place of `ancestors`, so mixed inheritance lists every ancestor there) | Kept as its own type. An entry without `_type` is read as a generic type. |
| `archetype_visualize_descendants_of`, Archie's spelling | Written as the spec's `archetype_visualise_descendants_of`. |
| A redundant `_type` on a `type_def` whose type is fixed by context (AM 2.x `c_conforms_to` parameters) | Dropped, as the spec allows. |
| Type objects inside `generic_parameters`, which the class model types as `List<String>` (AM 2.x) | Kept as objects. |
| `cardinality` on a `P_BMM_GENERIC_PROPERTY` (AM 2.4.0 `ARCHETYPE_TERMINOLOGY.value_sets`) | Dropped: the class defines no `cardinality`. |

## Adding a `P_BMM_*` variant

1. Add the concrete class under `src/Model/`, extending the matching
   `Abstract*` dispatcher, or the concrete class that parsed the variant
   until now, so existing `instanceof` checks keep matching.
2. Add a `match` arm for its `_type` in that dispatcher's `fromArray()`.
3. Unless it extends a class already in the dispatcher's union return type,
   add it there; otherwise PHP throws a `TypeError` and PHPStan reports the
   mismatch.
4. Add a round-trip test under `tests/TestCase/Model/`.

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
  valid P_BMM JSON payload that uses only the types and attributes the
  library models. Field defaults stripped by `array_filter` are the only
  intentional omissions (see invariant 4); the inputs that deviate from the
  class model are listed under [P_BMM 2.4 coverage](#p_bmm-24-coverage).
- Breaking changes to that contract — renaming JSON fields, changing
  `_type` discriminators, adding required fields without defaults, or
  removing dispatcher cases — require a **major** version bump.
- Adding new optional fields, new `_type` cases handled by a new
  concrete subclass, or new helper methods are **minor** bumps.
- Bug fixes that restore the documented round-trip behaviour are **patch**
  bumps.

`BmmSchema::BMM_VERSION` is the P_BMM version the library targets. It is
the default `bmm_version` when a payload has none; a payload's own value is
kept as is, without validation. When it changes (current `2.4`), update
test fixtures under `tests/resources/` and document the format delta in
[p-bmm-json-structure.md](p-bmm-json-structure.md).
