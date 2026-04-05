# P_BMM JSON (format 2.4): inferred structure for this project

This document summarises **non-normative, inferred** structure from the library’s **test fixtures** (`tests/resources/*.bmm.json`) and from implementation types under `src/Model/`. For **normative** definitions of `P_BMM_*` classes, use **[BMM Persistence Model and Syntax](https://specifications.openehr.org/releases/LANG/latest/bmm_persistence.html)** (STABLE).

## References

- [BMM Persistence Model and Syntax (latest)](https://specifications.openehr.org/releases/LANG/latest/bmm_persistence.html) — formal `P_BMM_*` package and attributes.
- [Basic Meta-Model (BMM) (latest)](https://specifications.openehr.org/releases/LANG/latest/bmm.html) — abstract `BMM_*` model that tools build after loading P_BMM.
- [openehr-bmm-landscape.md](openehr-bmm-landscape.md) — how BMM vs P_BMM versions relate and what `bmm_version` means in JSON.

## Top-level schema object

Top-level fields (composite view; not every field appears in every fixture):

| Field | Purpose |
|-------|---------|
| `bmm_version` | Serialisation / schema format version (fixtures use **`"2.4"`**; matches `BmmSchema::BMM_VERSION`). |
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

JSON objects that correspond to P_BMM classes carry a **`"_type"`** string so deserialisers know which subtype to instantiate. The fixtures in this repo use the following **union of discriminators** (sorted):

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

Normative names and extra variants (e.g. indexed containers) appear in the **persistence** specification; not every variant may appear in a given fixture.

## Class definitions: properties and `functions`

Each class-like entry (under `primitive_types`, `class_definitions`, or nested package class maps) may contain:

- **Identity**: `name`, `documentation`, `is_abstract`, `ancestors`, `generic_parameter_defs`.
- **`properties`**: Map of property name → object with `"_type"` (one of the `P_BMM_*_PROPERTY` kinds).
- **`functions`**: Map of operation name → **function** object:
  - `name`, optional `aliases`, `documentation`, `is_abstract`
  - `parameters`: map of parameter name → `P_BMM_*_FUNCTION_PARAMETER`
  - optional `result` (often `P_BMM_SIMPLE_TYPE` or other type objects)
  - optional pre/post conditions in some schemas (when present, mapped in the PHP model)

This is the **“extra” operational layer** emphasised by this library: P_BMM already models routines formally, but **JSON emitted for the openEHR RM** exposes rich **`functions`** blocks for foundation types (e.g. `Any`, `Hash`, `Container`) that are indispensable for **static reasoning** and **downstream code generation**.

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
2. Treat **`bmm_version`** as **file format** compatibility, not as “BMM spec 2.4” (see [openehr-bmm-landscape.md](openehr-bmm-landscape.md)).
3. Prefer **`AGENTS.md`** and this `docs/` tree for repository conventions; use the openEHR **persistence** spec when validating new `P_BMM_*` shapes or adding parser cases.
