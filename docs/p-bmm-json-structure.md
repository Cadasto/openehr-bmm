# P_BMM 2.4 JSON: structure as used by this project

This document summarises the P_BMM JSON form as the library reads it: the serialisation rules and attributes from the **[BMM Persistence Model and Syntax](https://specifications.openehr.org/releases/LANG/development/bmm_persistence.html)** specification (STABLE, P_BMM 2.4), what the **test fixtures** (`tests/resources/*.bmm.json`) contain, and how `src/Model/` maps it. For anything normative, the specification wins.

## References

- [BMM Persistence Model and Syntax (development)](https://specifications.openehr.org/releases/LANG/development/bmm_persistence.html): the `P_BMM_*` classes and their attributes ([Markdown twin](https://specifications.openehr.org/releases/LANG/development/bmm_persistence.md), without the class tables).
- [Basic Meta-Model (BMM) (development)](https://specifications.openehr.org/releases/LANG/development/bmm.html): the `BMM_*` model that tools build after loading P_BMM. `P_BMM_SCHEMA` inherits its header attributes from `BMM_SCHEMA_CORE`, defined here.
- [openehr-bmm-landscape.md](openehr-bmm-landscape.md): how BMM and P_BMM versions relate and what `bmm_version` means.

## Top-level schema object

Top-level fields of `P_BMM_SCHEMA` (composite view; not every field appears in every fixture):

| Field | Purpose |
|-------|---------|
| `bmm_version` | The P_BMM version the schema conforms to (fixtures use **`"2.4"`**; matches `BmmSchema::BMM_VERSION`). |
| `rm_publisher` | Publisher id (e.g. `openehr`). |
| `schema_name` | Short schema name (e.g. `base`, `rm`). |
| `rm_release` | Release tag for the RM slice described by this file. |
| `schema_revision` | Finer revision (e.g. `1.3.0.2`). |
| `schema_lifecycle_state` | e.g. `stable`. |
| `schema_description`, `schema_author` | Metadata strings. |
| `packages` | Nested package tree: names → sub-packages or class name lists. |
| `primitive_types` | Optional map of **primitive / foundation** type names → rich class definitions (often where **`functions`** appear). Present in `base`, absent in `rm`. |
| `class_definitions` | Optional map of additional class bodies (usage depends on generator). |
| `includes` | Optional **schema composition**: included schema ids (see below). Present in `rm`, absent in `base`. |
| `schema_contributors` | Optional list of contributing authors. |
| `archetype_parent_class`, `archetype_data_value_parent_class`, `archetype_rm_closure_packages`, `archetype_visualise_descendants_of` | Optional hints for archetype tooling (persisted attributes of `BMM_SCHEMA_CORE`). Absent from the fixtures and the published openEHR JSON schemas. |

### Schema identity and addressing

- **Logical schema id** (as in `BmmSchema::getSchemaId()`):  
  `"{rm_publisher}_{schema_name}_{rm_release}"`  
  Example: `openehr_base_1.3.0`, `openehr_rm_1.2.0`.

- **`includes`**: An object whose keys are include labels; values typically carry an `id` referencing another schema’s logical id, e.g. RM schema including base:

```json
"includes": {
    "openehr_base_1.3.0": {
        "id": "openehr_base_1.3.0"
    }
}
```

That pattern is the usual **cross-schema address** between JSON documents in a BMM directory layout. Resolution order is tool-dependent; the PHP IR holds the include graph as `BmmSchemaInclude` objects.

- **Package paths**: Keys under `packages` nest qualified namespaces (e.g. `org.openehr.base.foundation_types` → `primitive_types` → class list). **Class references** elsewhere are usually **unqualified short names** (`"Integer"`, `"DV_TEXT"`) resolved in the merged model.

## Polymorphism: the `_type` discriminator

Wherever a value can be one of several `P_BMM_*` subtypes (a property, a function parameter, a result or other type), the spec requires a **`"_type"`** string naming the concrete subtype; where the type is clear from context (a class definition, a package), no discriminator is needed. The fixtures in this repo use the following discriminators, all of which the library models:

| `_type` | Typical role |
|---------|----------------|
| `P_BMM_SIMPLE_TYPE` | Named type reference (`type`: class name string). |
| `P_BMM_GENERIC_TYPE` | Generic application (e.g. `List<T>` with generic parameters). |
| `P_BMM_CONTAINER_TYPE` | Container kind + item type. |
| `P_BMM_SINGLE_PROPERTY` | Single-valued property. |
| `P_BMM_SINGLE_PROPERTY_OPEN` | Open (generic) single property. |
| `P_BMM_CONTAINER_PROPERTY` | Multi-valued property. |
| `P_BMM_GENERIC_PROPERTY` | Property typed with generic parameters. |
| `P_BMM_SINGLE_FUNCTION_PARAMETER` | Parameter with fixed referenced type name. |
| `P_BMM_SINGLE_FUNCTION_PARAMETER_OPEN` | Parameter typed with an **open** type variable (e.g. `K`, `T`). |
| `P_BMM_CONTAINER_FUNCTION_PARAMETER` | Parameter that is itself a container type. |
| `P_BMM_GENERIC_FUNCTION_PARAMETER` | Generic parameter in a routine signature. |
| `P_BMM_ENUMERATION_STRING` / `P_BMM_ENUMERATION_INTEGER` | Enumerated types. |
| `P_BMM_INTERFACE` | Interface definition. |

The specification defines three more concrete discriminators. Neither the fixtures nor the published openEHR JSON schemas use them (the latter express `Hash` properties as `P_BMM_GENERIC_PROPERTY` with `root_type: "Hash"`), but the library models them:

| `_type` | Role | PHP class |
|---------|------|-----------|
| `P_BMM_INDEXED_CONTAINER_PROPERTY` | Property of an indexed container type such as `Hash<String, EVENT_ACTION>`; `type_def` adds `index_type`. | `BmmIndexedContainerProperty` (a `BmmContainerProperty`) |
| `P_BMM_INDEXED_CONTAINER_TYPE` | Indexed container type reference (`container_type`, `index_type`, `type`). | `BmmIndexedContainerType` (a `BmmContainerType`) |
| `P_BMM_OPEN_TYPE` | Reference to an open generic parameter such as `T`. | `BmmOpenType` (a `BmmSimpleType`) |

## Class definitions: properties and `functions`

Each class-like entry (under `primitive_types`, `class_definitions`, or nested package class maps) may contain:

- **Identity**: `name`, `documentation`, `is_abstract`, `ancestors`, `generic_parameter_defs`. For generic inheritance the spec uses `ancestor_defs` instead of `ancestors`: a `List<P_BMM_GENERIC_TYPE>` in the class model, a map keyed by type signature in the spec's example. The library reads both and writes the shape it read.
- **`properties`**: Map of property name → object with `"_type"` (one of the `P_BMM_*_PROPERTY` kinds).
- **`functions`**: Map of function name → `P_BMM_FUNCTION` object:
  - `name`, optional `aliases` (keyed by alias), `documentation`, `is_abstract`
  - `parameters`: map of parameter name → `P_BMM_*_FUNCTION_PARAMETER`
  - optional `result` (a `P_BMM_*_TYPE` object); a function without a `result` is a procedure
  - optional `is_nullable` (the result may be `Void`)
  - optional `pre_conditions` and `post_conditions`, each a map of tag → assertion text
- **`constants`**: Map of constant name → `P_BMM_CONSTANT` with `type` and a serialised literal `value`.
- **`invariants`**: Map of tag → assertion text (usually BEL); P_BMM treats the text as opaque.

Functions, constants and invariants are the features P_BMM 2.4 added. The library models all of them; the spec notes that other BMM tooling often ignores them. In the openEHR JSON schemas the foundation types (for example `Any`, `Hash`, `Container`) carry rich `functions` blocks, which matter for static reasoning and code generation.

## Minimal fragments (illustrative)

**Function with open generic parameter** (pattern from `Any` / container types):

```json
"parameters": {
    "a_key": {
        "_type": "P_BMM_SINGLE_FUNCTION_PARAMETER_OPEN",
        "name": "a_key",
        "type": "K"
    }
}
```

**Result type**:

```json
"result": {
    "_type": "P_BMM_SIMPLE_TYPE",
    "type": "Boolean"
}
```

**Property discriminant**:

```json
"some_prop": {
    "_type": "P_BMM_SINGLE_PROPERTY",
    "name": "some_prop",
    "type": "DV_TEXT"
}
```

## Practical notes for AI / tooling

1. Always read **`_type`** before interpreting sibling keys; optional fields vary by subtype.
2. **`bmm_version`** is the P_BMM version (2.4), not the version of the BMM or BMM 3 documents (see [openehr-bmm-landscape.md](openehr-bmm-landscape.md)).
3. Prefer **`AGENTS.md`** and this `docs/` tree for repository conventions; use the openEHR **persistence** spec when validating new `P_BMM_*` shapes or adding parser cases. The class tables are in the HTML page, not the Markdown twin.
