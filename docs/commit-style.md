# Commit style

Canonical commit-message format for every commit made by humans, the
Cursor/Claude/Junie commit UIs, or any agent. Also referenced from
[.cursor/rules/commit-messages.mdc](../.cursor/rules/commit-messages.mdc).

## Format

```
type: imperative short description

Optional body after a blank line. Wrap at ~72 chars.
```

- **One-line subject**: `type: imperative short description`
- **Subject ≤ ~72 characters**, imperative mood (`add`, `fix`, **not**
  `added` / `Enhances…`).
- **Optional body** after a blank line, for context, motivation, or
  trade-offs the diff alone doesn't show.
- **Trailers** (e.g. `Co-Authored-By:`) at the end of the body, after a
  blank line.

## Allowed types

`feat`, `fix`, `chore`, `docs`, `refactor`, `test`, `ci`, `build`,
`style`, `perf`.

## One-line hint for generating

`conventional commit, <72 chars, feat/refactor/fix`

## Examples

**Good:**

```
refactor: map generic params in BmmGenericType::fromArray
```

```
fix(model): round-trip BmmGenericType.generic_parameters cleanly

toArray() previously emitted live AbstractBmmType objects inside
generic_parameters; consumers reading the array as plain data broke.
Map elements symmetrically so strings pass through and objects are
converted via ->toArray().
```

**Bad:**

```
Enhance BmmGenericType::fromArray method to process generic parameters,
ensuring proper conversion of nested type-defs and preserving all
existing behaviour for string elements
```

(no `type:` prefix; subject too long; non-imperative; explains the diff
instead of the *why*).

## Scope (optional)

Scope in parentheses after the type narrows where the change lands:
`fix(model): …`, `refactor(helper): …`, `chore(ci): …`. Use scopes that
match top-level source directories or `ci` / `docs` for cross-cutting work.
