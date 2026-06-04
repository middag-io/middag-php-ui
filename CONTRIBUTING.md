# Contributing

Thanks for your interest in `middag-io/ui`. This package owns the UI **contract
vocabulary** (builders, value objects, enums, JSON schemas) shared between
`middag-io/framework`, the host adapters, and the SPA renderer. This guide covers
the workflow, coding standards, and quality pipeline the project expects.

## Scope

This library is **contract-first** and a **leaf** (zero runtime dependencies). It
produces data; it does not render. It does **not** own:

- **Renderer implementations** (Inertia, host UIs) — those live in
  `middag-io/framework` and the host adapters.
- **Validation engines** — the form validator lives in `middag-io/framework`.
- **Persistence** — none, by design.

New public surface only lands with a real use case — no speculative contracts.
If you want to add behaviour, double-check it belongs here (a generic, renderer-
agnostic contract) rather than downstream (a renderer or host concern).

## Workflow

1. Fork and clone.
2. Create a feature branch off `develop`.
3. Run the full check suite locally before pushing: `composer check && composer test`.
4. Open a pull request against `develop`.

## Coding standards

### Typing and formatting

- `declare(strict_types=1);` at the top of **every** PHP file, no exceptions.
- The style is **PSR-12 plus the `@PhpCsFixer` ruleset**, enforced by
  PHP-CS-Fixer. `camelCase` for methods and properties, `PascalCase` for classes.
- **PSR-4**: the namespace mirrors the path — `Middag\Ui\<Concern>\...`.
- Explicit types on every signature. Target PHP `^8.2`; use enums, `readonly`,
  and `final` where they fit. Fluent builders return `static`.
- The Apache-2.0 license header is applied automatically by PHP-CS-Fixer — run
  `composer fix` and it is added for you.

### Naming by type tier

The tree is **concern-first**: each concern (`Action/`, `Block/`, `Form/`,
`Page/`, `Table/`, …) is a top-level directory. Put each symbol where it belongs:

| Symbol            | Directory               | Convention                                       |
| ----------------- | ----------------------- | ------------------------------------------------ |
| Interface         | `<Concern>/Contract/`   | mandatory `*Interface` suffix, no implementation |
| Backed enum       | `Shared/Enum/`          | closed catalog (`: string` / `: int`), immutable |
| Cross-cutting VO  | `Shared/Data/`          | `readonly class`, dumb data                      |
| Builder / VO      | concern root            | fluent builders return `static`; leaf VOs `final readonly` |

An interface always ends in `*Interface` and lives in the concern's `Contract/`.

### Docblocks and the public surface

- `@api` marks the stable public surface: what consumers may call and what the
  generated reference documents. It follows semver.
- `@internal` marks implementation detail that may change without notice.
- A docblock explains the **why** and the non-obvious contracts (immutability,
  trust boundaries, wire shape). Do not restate a typed signature.

### Tests

- Cover **new behaviour** with a test. `tests/` mirrors `src/`; every test
  declares `#[CoversClass]` (or `#[CoversNothing]` for pure value/enum pins).
- Backed enums pin their exact wire values; the emitted schemas have round-trip
  and strictness (discriminator / `additionalProperties:false`) coverage.

## Quality gates

Everything green before you push:

```bash
composer check    # PHP-CS-Fixer + Rector (dry-run) + PHPStan (level 6) + schema check
composer test     # PHPUnit
composer fix      # auto-fix: PHP-CS-Fixer + Rector
composer fix:all  # style → rector → style (re-settles formatting after Rector)
```

- PHPStan runs at **level 6** with zero new errors.
- PHP-CS-Fixer and Rector must be clean (the dry-run shows no diff).
- `composer check` runs `bin/emit-schemas.php --check`: the committed
  `schema/*.json` are published artifacts and must match `SchemaRegistry`.
  Regenerate with `composer emit:schemas` when you change a wire shape.

## Commit messages and branch

[Conventional Commits](https://www.conventionalcommits.org/):

```
type(scope): short summary

Longer body when the "why" isn't obvious.
```

- Types: `feat`, `fix`, `refactor`, `perf`, `docs`, `style`, `test`, `build`,
  `ci`, `chore`, `revert` — the set `release-please` maps to CHANGELOG sections.
- One scope per commit, lowercase — the `commit-msg` hook rejects
  comma-separated multi-scope subjects.
- Mark a breaking change with `!` (e.g. `feat(table)!: …`) or a
  `BREAKING CHANGE:` footer; the hook accepts both.
- **Never** add `Co-Authored-By` trailers.
- The branch base is **`develop`**.

### Versioning (release-please)

`release-please` reads the commit history to compute the next version and the
CHANGELOG, so the commit **type** drives the bump.

While the package is **pre-1.0** (`release-please-config.json` sets
`bump-minor-pre-major` and `bump-patch-for-minor-pre-major`):

| Commit | Bump while `0.x` |
| ------ | ---------------- |
| `fix:` / `feat:` | PATCH |
| breaking (`!` or `BREAKING CHANGE:`) | MINOR |

The public API is allowed to move while `0.x`, so no single commit auto-produces
a MAJOR yet.

**Going to `1.0.0` (and MAJOR releases after):** when the public API is declared
stable, cut `1.0.0` deliberately — land a commit whose footer is
`Release-As: 1.0.0` (or bump `.release-please-manifest.json` directly). From
`1.0.0` onward the pre-major flags become inert and standard semver applies:
`fix:` → PATCH, `feat:` → MINOR, and a breaking change → MAJOR. Remove
`bump-minor-pre-major` / `bump-patch-for-minor-pre-major` from
`release-please-config.json` at that point for clarity.

## Pull request checklist

1. `declare(strict_types=1);` in every new file.
2. Interfaces in `<Concern>/Contract/` with the `*Interface` suffix; enums in
   `Shared/Enum/`; cross-cutting VOs in `Shared/Data/`.
3. `@api` / `@internal` marked on the surface you touched.
4. New behaviour covered by a test; wire shapes pinned.
5. `composer check && composer test` green (schemas regenerated if a wire shape changed).
6. Boundaries respected: no host APIs, nothing renders (data only), the library
   stays agnostic and dependency-free.
7. Conventional Commit, no `Co-Authored-By`, single scope.

## Architecture

The full technical reference — the PageContract system, the concern-first
layout, the three composition levels, and the OSS boundary — lives in
[`docs/architecture.md`](https://github.com/middag-io/middag-php-ui/blob/main/docs/architecture.md)
and is published at [docs.middag.dev](https://docs.middag.dev).

## Code of conduct

This project follows the [`CODE_OF_CONDUCT.md`](https://github.com/middag-io/middag-php-ui/blob/main/CODE_OF_CONDUCT.md). By
participating you agree to uphold it.

## Security

Found a security issue? Follow [`SECURITY.md`](https://github.com/middag-io/middag-php-ui/blob/main/SECURITY.md). Please do not open a
public issue for vulnerabilities.

## License

By contributing you agree your contribution is released under the Apache License
2.0, the same license as the project.
