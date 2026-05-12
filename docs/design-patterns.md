# Design and patterns

Recommendations, **not enforced**. Prefer established design patterns
where they fit; do not enforce a single standard. Consistency within a
single library is more important than applying every pattern everywhere.

## Patterns to reach for

- **PSR interfaces**. When wrapping or extending behaviour (HTTP, logging,
  caching, etc.), consider implementing or depending on the relevant PSR
  (e.g. PSR-3 Logger, PSR-6/16 cache, PSR-7 HTTP messages) so the library
  composes well with the rest of the ecosystem.
- **Factory pattern**. Use when object creation is non-trivial or when
  you want to centralise construction (e.g. for test doubles or
  different implementations). The polymorphic `fromArray()` dispatchers
  on `AbstractBmmClass`, `AbstractBmmProperty`,
  `AbstractBmmFunctionParameter`, and `AbstractBmmType` are the canonical
  example in this codebase.
- **Decorator pattern**. Useful to add behaviour around a PSR or other
  interface without changing the original type (e.g. logging or caching
  decorators).
- **Dependency injection**. Prefer constructor injection and small,
  focused classes; avoid global state where possible.
- **Immutability**. Prefer immutable value objects and read-only
  interfaces where it simplifies reasoning and usage. All model classes
  in `src/Model/` are `readonly` for this reason.

## When in doubt

Choose patterns that suit the problem in front of you. A single
well-tested, well-named class beats a constellation of premature
abstractions. See [architecture.md](architecture.md) for the model-layer
invariants that *are* enforced.
