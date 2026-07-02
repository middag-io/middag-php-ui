# CLAUDE.md — middag-io/ui

> A **durable** orientation guide for the agent: what this package is, its boundaries, conventions and
> workflow. **Not a file index** (an index breaks on every move). To locate symbols, use Glob/Grep.
> **The codebase is the source of truth.** Live structure/counts come from `src/`, not from here.

## Mental model in 30s

MIDDAG's **UI contract-builder** library: a transport-agnostic system for describing a page **once**
and rendering it **anywhere**. Builders produce an immutable **page contract** (`JsonSerializable` →
JSON) that a host/adapter or `@middag-io/react` turns into UI — over Inertia or any transport.

- **Transport-agnostic** — builders produce `JsonSerializable` value objects; no Inertia/transport dependency.
- **Zero runtime dependencies** — PHP `^8.2` only. Consumers inherit nothing transitive.
- **Host-agnostic** — knows nothing about Moodle, WordPress, or any host. No `mform`, `wpdb`,
  capability calls, column names, or plugin conventions. Anything host-specific lives in the consuming
  adapter, never here.
- **Closed by intent** — new public surface only lands with a real use case (no speculative contracts).
- **Three composition levels** — L1 convention, L2 convention + overrides, L3 free composition.

## Rule #1 — the OSS boundary (the invariant everything respects)

This package is the **bottom** of the MIDDAG stack: it defines the generic contracts and produces
data. **Rendering, host wiring, and HTTP transport live downstream**, never here.

- **No contract here produces HTML** (`render(): string`). Rendering is a product/host concern and
  lives in `middag-io/framework` and the adapters. ui contracts produce **data / value objects**
  (e.g. `FormRendererInterface` → `RendererOutput`, not a string).
- **No proprietary or host concepts** anchor this surface — no tier/commercial language, no named
  host APIs, in code or docblocks.
- **Authorization is data, not a call:** opaque authorization tokens (e.g. the `capability` field on
  `Action`/`NavigationNode`/`CrudBuilder`) are **data** the contract may carry. What is forbidden is
  *calling* a host API (`has_capability`/`mform`/`wpdb`). The adapter resolves the token; ui only carries it.

## How the code is organised (the rule, not the list)

**Concern-first** at the root of `src/`: each *concern* is a top-level directory (`Action/ Block/
Condition/ Envelope/ Form/ Inspector/ Navigation/ Page/ Region/ Table/`, plus `Shared/`). The `src/`
root hosts the page-contract entry points and the root envelope.

Inside each concern:

- **`@api` interfaces** live in the concern's `Contract/` sub-namespace (suffix `Interface`); never
  put anything concrete in an interface.
- **fluent builders** return `static` and produce value objects.
- **value objects** are `readonly` (serializable when they go on the wire). A VO paired with a
  dedicated `*Interface` extension seam may be `readonly` (non-`final`) by design; a leaf VO is
  `final readonly`.
- **backed enums** (closed catalogs), cross-cutting VOs and helpers live in `Shared/`
  (`Shared/Enum/`, `Shared/ValueObject/`, `Shared/Concerns/`); the schema registry lives in the
  top-level `Schema/` concern.

> The wire value objects are an intentionally interlinked cluster (an envelope can embed any payload,
> a builder can emit a full page), so "concern" is an organisational axis, not an acyclic dependency
> boundary.

### Design invariants

| Rule | Why |
|------|-----|
| Zero external dependencies | Consumers inherit no unwanted transitivity |
| `Shared/ValueObject/` are `readonly class` | Immutability guaranteed at compile time |
| No host leak (Moodle/WordPress/`mform`/etc.) | Agnostic lib; host-specifics live in the adapter |
| Renderers and field-mappers live in `framework`/adapters, not here | ui hosts only contracts + layout primitives |
| No ui contract produces HTML (`render(): string`) | Rendering is a product/host concern (downstream) |
| `FieldDefinition` and `Condition` do NOT implement `JsonSerializable` | They are boundary objects; renderers map them — avoids coupling to the wire format |
| `@api` interfaces in each concern's `Contract/`; concrete VOs outside interfaces | Concern-first axis |

## How to work here

- **Gates (everything green before any delivery):** `composer check` (php-cs-fixer + Rector +
  PHPStan + schema check) **&&** `composer test` (PHPUnit). Auto-fix: `composer fix`.
- **Schemas:** the emitted JSON schemas under `schema/` are published artifacts (codegen consumes
  them). `composer check` runs `bin/emit-schemas.php --check` to catch drift; regenerate with
  `composer emit:schemas`.
- **Style:** `declare(strict_types=1)` in every file; PSR-4 root namespace `Middag\Ui\`; camelCase;
  fluent builders return `static`; cover new behaviour with a test (`tests/` mirrors `src/`,
  `#[CoversClass]`). `@api` = public surface (enters the generated xRef/docs); `@internal` = internal.
- **Commits:** Conventional Commits; **NEVER** `Co-Authored-By`. Single lowercase scope. Mark breaking
  changes with `!` or a `BREAKING CHANGE:` footer. Branch base: `develop`. Pre-1.0: feat/fix → patch,
  breaking → minor.

## Relationship to other packages

```
middag-io/ui (this repo)        ← zero deps, host-agnostic
  └─ middag-io/framework        ← requires ui; generic renderers/kernel
       ├─ middag-io/moodle      ← requires framework; Moodle adapters + host-specifics
       └─ middag-io/wordpress   ← requires framework; WordPress adapters
  └─ @middag-io/react (NPM)     ← consumes the JSON this package produces
```

## State

Pre-1.0 public release (Apache-2.0, staying `0.x` until the API is stable). Technical docs live in
**`docs/`** and are published at **docs.middag.dev**.
