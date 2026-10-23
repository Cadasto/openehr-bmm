# openEHR BMM and P_BMM: specification landscape

This note orients contributors and AI tooling to how the **Basic Meta-Model (BMM)** and its **persistence model (P_BMM)** relate, what the version numbers mean, and how the **Cadasto `openehr-bmm` PHP library** fits in. It follows the **development** versions of the openEHR LANG documents, as checked on 2026-10-23; release pages under `latest` can lag them.

## References (authoritative)

| Document | Role | Status | URL |
|----------|------|--------|-----|
| **Basic Meta-Model (BMM)** | Abstract meta-model: the in-memory `BMM_*` classes (models, packages, classes, types, properties) | STABLE | [bmm.html](https://specifications.openehr.org/releases/LANG/development/bmm.html) |
| **BMM Persistence Model and Syntax (P_BMM)** | Serialisable `P_BMM_*` classes, schema inclusion, ODIN / JSON / YAML syntax | STABLE, P_BMM version **2.4** | [bmm_persistence.html](https://specifications.openehr.org/releases/LANG/development/bmm_persistence.html) ([Markdown](https://specifications.openehr.org/releases/LANG/development/bmm_persistence.md)) |
| **BMM 3** | The restructured v3 line, adding an expression meta-model | PAUSED | [bmm3.html](https://specifications.openehr.org/releases/LANG/development/bmm3.html) |

Per-class attribute tables are in the HTML pages, not the Markdown twins.

## Version numbers

1. **P_BMM carries its own version number, currently 2.4**, "independent of any single openEHR component release". Version 2.4 "formalises the capture of the full feature set of a class: functions (routines) with their parameters, constants, and class invariants, in addition to the properties already supported in earlier versions" (P_BMM, *Versions*).

2. **`bmm_version` in a schema file is that P_BMM version.** The spec defines `P_BMM_SCHEMA.bmm_version` as the "Version of the BMM (P_BMM) meta-model to which this schema conforms, e.g. `2.4`". This repository's fixtures carry `"2.4"`, matching `BmmSchema::BMM_VERSION`. The ODIN header example in the spec text still shows `"2.3"`.

3. **BMM and BMM 3 are separate documents.** The stable BMM document defines the in-memory `BMM_*` classes that P_BMM is transformed into. The v3 line (BMM 3) is paused. A "BMM 3.x" number refers to that paused document, not to anything a schema file records.

## What P_BMM is for (per specification)

The persistence specification states that:

- The initial materialised form of a schema file is "a graph of `P_BMM_XXX` classes, with a subsequent in-memory model-to-model transform step required to produce a materialised BMM model".
- `.bmm` files are schemas that support inclusion and re-use, so one logical model can span several files; a reader resolves the inclusions before building `BMM_*` objects.
- The `P_BMM_*` classes are a "model of a BMM *schema*" and the `BMM_*` classes a "model of a BMM *model*", the latter with all name references resolved to object references.

So any **JSON BMM file** you edit or parse is **P_BMM-shaped**, even when people casually say "BMM file".

## Serialisation formats

The spec allows any format that can express a typed object model and names three in use:

| Format | Extension | Notes |
|--------|-----------|-------|
| ODIN | `.bmm` | The original format, with the widest tool support (for example the ADL Workbench). |
| JSON | `.bmm.json` | "The primary format produced and consumed by the `bmm-publisher` tooling". This library reads and writes this form. |
| YAML | `.bmm.yaml` | A more compact alternative, also produced by `bmm-publisher`. |

The same rules hold in every format: wherever a value can be one of several `P_BMM_*` subtypes, it carries a `_type` discriminator (JSON `"_type": "P_BMM_SINGLE_PROPERTY"`), and keyed collections such as `class_definitions`, `properties`, `functions`, function `parameters` and `packages` are keyed by name.

## Functions, constants and invariants

These P_BMM 2.4 features "exist primarily to capture the operation specifications of the openEHR models", which moved from UML to the `bmm-publisher` tooling in 2026. The spec notes that they "are not yet widely adopted by BMM tooling", and that a tool materialising only the structural features "may safely ignore" them. Invariants are assertion strings keyed by tag, usually in BEL, which P_BMM treats as opaque.

## This repository's role

**`cadasto/openehr-bmm`** is a **PHP intermediate representation (IR)** of **P_BMM 2.4 JSON**:

- It maps persisted objects, discriminated with `_type` such as `P_BMM_SINGLE_PROPERTY` or `P_BMM_GENERIC_TYPE`, to typed PHP value objects.
- Unlike tools that ignore them, it models the 2.4 class features: `functions` (parameters, result, pre- and post-conditions), `constants`, and `invariants`.
- It does not yet model every persisted type and attribute. The gaps, and what happens to them on load, are listed in [architecture.md](architecture.md#known-gaps-against-p_bmm-24).

For the JSON shape, the discriminator inventory, and schema addressing (ids, includes, package paths), see **[p-bmm-json-structure.md](p-bmm-json-structure.md)**.
