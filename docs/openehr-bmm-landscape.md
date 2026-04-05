# openEHR BMM and P_BMM: specification landscape

This note orients contributors and AI tooling to how the **Basic Meta-Model (BMM)** and its **persistence layer (P_BMM)** relate, why version numbers look “messy”, and how the **Cadasto `openehr-bmm` PHP library** fits in.

## References (authoritative)

| Document | Role | Typical URL |
|----------|------|-------------|
| **Basic Meta-Model (BMM)** | Abstract meta-model (packages, types, classes, features, expression meta-model, …) | [LANG latest — BMM](https://specifications.openehr.org/releases/LANG/latest/bmm.html) |
| **BMM Persistence Model and Syntax (P_BMM)** | Serialisable “P_BMM_*” types, schema composition, ODIN/JSON/YAML/XML | [LANG latest — BMM persistence](https://specifications.openehr.org/releases/LANG/latest/bmm_persistence.html) |

Status lines on those pages (as of review for this doc): **BMM** is published as **TRIAL**; **P_BMM** as **STABLE**. Both ship under **LANG Release-1.0.0** as a release bundle, but each has its **own amendment history** inside the document.

## Why two “version” stories exist

1. **BMM (abstract)** evolves with new conceptual types and restructuring. The published amendment record includes entries such as **3.1.0** (e.g. additions like decision/action table concepts, interval values, feature groups, visibility, and refinements to expressions and routines). That is **not** the same number as the persistence spec.

2. **P_BMM (persistence)** is the simplified, serialisable projection used in `.bmm` files (historically **ODIN**; **JSON with type markers** is explicitly allowed). Its amendment record is separate; it includes milestones such as **2.3.0** when P_BMM was split from the main BMM text and gained types like indexed containers.

So “**BMM ≈ v3**” and “**P_BMM ≈ v2.3**” in conversation usually refer to **those independent amendment lines**, not to a single global semver for “openEHR meta-models”. (Note: the `bmm_version: “2.4”` found in JSON files is a **file-format version**, not the P_BMM amendment id — see point 3 below.)

3. **`bmm_version` in JSON files** (e.g. `"2.4"` in this repository’s fixtures) is a **schema / serialisation format version** emitted by tools (e.g. ADL Workbench–family generators). It is **not** automatically identical to either the BMM document amendment id or the P_BMM persistence amendment id. Treat it as the **payload format version** the library round-trips (see `BmmSchema::BMM_VERSION` in code).

## What P_BMM is for (per specification)

The persistence specification states that:

- Materialised files are a graph of **`P_BMM_*`** classes (with symbolic names and simplified structure).
- A tool typically performs a **model-to-model transform** from that graph to full **`BMM_*`** instances in memory.
- **`P_BMM_*`** is described as a “model of a BMM **schema**”; full **`BMM_*`** is a “model of a BMM **model**” with references resolved.

So any **JSON BMM file** you edit or parse is almost always **P_BMM-shaped**, even when people casually say “BMM file”.

## Alignment gaps (why it feels messy)

- New **BMM** concepts can appear in the **abstract** specification before **P_BMM** gains matching persistence types or before generators emit them.
- Conversely, shipped **JSON schemas** may carry a **`bmm_version`** and **`_type`** discriminators that reflect **tool output**, while the normative **class catalogue** for P_BMM is defined in the **STABLE persistence** document.
- This library intentionally targets **P_BMM JSON** as used in the wild (see [p-bmm-json-structure.md](p-bmm-json-structure.md)) plus **class operation (`functions`)** blocks where present in those files.

## This repository’s role

**`cadasto/openehr-bmm`** is a **PHP intermediate representation (IR)** of **P_BMM JSON**:

- It maps persisted objects (discriminated with `_type` such as `P_BMM_SINGLE_PROPERTY`, `P_BMM_GENERIC_TYPE`, …) to typed PHP value objects.
- It supports **class-level `functions`** (signatures, parameters, result types) as they appear in published openEHR BMM JSON (e.g. under `primitive_types` / class maps in the test fixtures). Those operational definitions are **essential for expression typing and tooling** but are easy to overlook when reading only the high-level BMM class diagrams.

For hands-on JSON shape, discriminator inventory, and schema “addressing” (ids, includes, package paths), see **[p-bmm-json-structure.md](p-bmm-json-structure.md)**.
