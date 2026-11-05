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
- **Three builder levels** — L1 convention, L2 convention + overrides, L3 free composition. A fourth
  level (L4, a dedicated frontend page) has no builder here: the consuming product owns it.

## Rule #1 — the OSS boundary (the invariant everything respects)

This package is the **bottom** of the MIDDAG stack: it defines the generic contracts and produces
data. **Rendering, host wiring, and HTTP transport live downstream**, never here.

- **No contract here produces HTML** (`render(): string`). Rendering is a product/host concern and
  lives in `middag-io/framework` and the adapters. ui contracts produce **data / value objects**
  (e.g. `FormRendererInterface` → `RendererOutput`, not a string).
- **No proprietary or host concepts** anchor this surface — no edition or commercial labels, no named
  host APIs, in code or docblocks.
- **Authorization is data, not a call:** opaque authorization tokens (e.g. the `capability` field on
  `Action`/`NavigationNode`/`CrudBuilder`) are **data** the contract may carry. What is forbidden is
  *calling* a host API (`has_capability`/`mform`/`wpdb`). The adapter resolves the token; ui only carries it.

## How the code is organised (the rule, not the list)

**Concern-first** at the root of `src/`: each *concern* is a top-level directory (`Action/ Block/
Condition/ EditablePanel/ Envelope/ Form/ Inspector/ Navigation/ Page/ Region/ Table/`, plus `Shared/`). The `src/`
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
- **Commits:** Conventional Commits; **NEVER** `Co-Authored-By`. Single lowercase scope. Branch base: `main`.
  Versioning follows `docs-middag-ops` ADR-032 (version family of the `middag-php-*` libs). On
  `1.x`: fix → patch, feat → minor; never `!` or a `BREAKING CHANGE:` footer (either would cut
  `2.0.0` in this lib alone) and no `Release-As:` outside an open window — the version family
  check fails them; a break ships as
  `feat:` with the break in the body, only until 2026-11-30 (exception to `MSR-015.R1(b)`), then no
  break on `1.x`. From `2.0.0`: shared MAJOR.MINOR across the family, patch = fixes only, `feat`
  waits for a family MINOR window, `!` for a family MAJOR window; the window is the organization
  variable `PHP_LIBS_OPEN_RELEASE`, set on the order of the product owner, who decides every family
  MINOR and MAJOR. Keep a waiting PR current by merging `main` (never rebase or force-push) —
  policy in [`CONTRIBUTING.md`](CONTRIBUTING.md) §Versioning and [`API-STABILITY.md`](API-STABILITY.md).

## Decision records

Architecture decisions live in [`docs/decisions/`](docs/decisions/) as `UI-NNN` records in the
MIDDAG-MADR compact format (`docs-middag-ops` ADR-014): frontmatter, then Context → Considered Options
→ Decision → Consequences → Enforcement. A record states **why** (the choice, the alternatives, the
consequences); specification (class lists, catalogs, wire shapes) lives in the reference docs its
Enforcement table points to, mostly at docs.middag.dev (`docs-middag-dev`, `docs/framework/`). Ids are
never reused. OSS records never cite proprietary record ids. `deciders` is a role id (`leadership`),
never a person.

**`domains` vocabulary** (a record uses only these): `ui`, `frontend`, `contract`, `schema`,
`portability`.

## Relationship to other packages

```
middag-io/ui (this repo)        ← zero deps, host-agnostic
  └─ middag-io/framework        ← requires ui; generic renderers/kernel
       ├─ middag-io/moodle      ← requires framework; Moodle adapters + host-specifics
       └─ middag-io/wordpress   ← requires framework; WordPress adapters
  └─ @middag-io/react (NPM)     ← consumes the JSON this package produces
```

## State

Public on the **`1.x`** line (Apache-2.0). Until 2026-11-30 a minor may carry a breaking change
described in its commit body; from 2026-12-01 `1.x` takes no break. `2.0.0` is cut for the whole
`middag-php-*` family at once, on the decision of the product owner; from then on a minor never
breaks — the policy is [`CONTRIBUTING.md`](CONTRIBUTING.md) §Versioning. Technical docs live in **`docs/`** and
are published at **docs.middag.dev**.
