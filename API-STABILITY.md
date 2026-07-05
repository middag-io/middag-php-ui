# API Stability

This document defines what is public and supported in `middag-io/ui`, and how
the public surface may evolve during the current **`1.x`** line, so downstream
packages — `middag-io/framework` (which implements the `Form` render seam), the
OSS adapters (`middag-io/moodle`, `middag-io/wordpress`), the `@middag-io/react`
client that consumes the wire JSON, and any proprietary consumer built on top —
can depend on the contract builders without guessing.

`middag-io/ui` is the **foundation layer** of the MIDDAG stack (`ui` →
`framework` → the Moodle/WordPress adapters). It has **zero external
dependencies** and knows nothing about any host or transport: it only describes
pages as a `PageContract` (JSON).

## Stability levels

Every type carries a class-level annotation that states its stability:

| Annotation | Meaning |
|---|---|
| `@api` | **Public, supported surface.** You may implement, extend, type-hint, instantiate and catch these. Changes follow the versioning policy below. |
| `@internal` | **Implementation detail.** May change or be removed in any release, including patches. Do not depend on these from outside the package. |

If a type has neither annotation, treat it as `@internal`. Every type in `src/`
carries exactly one of the two tags.

The public surface is the set of `@api`-annotated types: the interfaces under
each concern's `Contract/` sub-namespace (`Page/Contract/`, `Form/Contract/`,
`Table/Contract/`, `Block/Contract/`, `Region/Contract/`, `Action/Contract/`,
`Navigation/Contract/`, `Inspector/Contract/`, `Envelope/Contract/`), the
concern builders and their value objects, and the cross-cutting types under
`Shared/`.

## The wire contract

Beyond the PHP surface, `middag-io/ui` has a second, stronger compatibility
contract: the **`PageContract` wire JSON** emitted for `@middag-io/react`. The
JSON shape is the real integration boundary with the client. A PHP-level rename
that leaves the emitted JSON unchanged is not a wire break; a change to the JSON
shape is, and is governed by the same `1.x` policy below.

## How releases are cut

Releases are cut **exclusively** by
[release-please](https://github.com/googleapis/release-please) from
[Conventional Commits](https://www.conventionalcommits.org/). There are no
manual tags: the version is derived from the commit type (`fix:` → patch,
`feat:` → minor), or set deliberately by a maintainer with a `Release-As:`
footer.

## The `1.x` policy

This mirrors the family-wide policy defined in the framework's
[`API-STABILITY.md`](https://github.com/middag-io/middag-php-framework/blob/main/API-STABILITY.md).
During the `1.x` line the API is **still consolidating**:

- **Patch** (`1.y.Z`) — bug fixes and `@internal`-only changes. Never a breaking
  `@api` (or wire) change.
- **Minor** (`1.Y.0`) — additive `@api` changes (new builders, new optional
  parameters, promoting an `@internal` symbol to `@api`). A minor **may also
  carry a breaking `@api` change** while the API consolidates. Every breaking
  change is explicitly marked in the history (`feat!` / a `BREAKING CHANGE:`
  footer) and listed in the CHANGELOG's **⚠ BREAKING CHANGES** section. Such
  releases are always cut deliberately by a maintainer with a `Release-As:`
  footer — never as an accidental side effect of merging.

Full strict-semver rigor — breaking changes **only** in major releases — starts
at `2.0`. A major release is never cut automatically: it happens only by
explicit maintainer decision, when the break genuinely impacts Composer
consumers — a release PR proposing a major bump is not merged without that
sign-off.

> Historical note: `1.2.1` shipped the audit-consolidation breaking change (the
> `TableOptions` → `TableDisplayOptions` rename) as a patch by explicit
> maintainer decision, closing the OSS audit before external consumers existed.
> The `0.6.0` concern-first layout move (every `Middag\Ui\*` FQN relocated)
> predated `1.0`, so shipped without shims. From this document on, a breaking
> `@api` change never lands in a patch.

## The cross-package seam

`middag-io/ui` defines the contract and `middag-io/framework` provides the
runtime implementation of one seam:

| Contract | Role |
|---|---|
| `Middag\Ui\Form\Contract\FormRendererInterface` | The render seam a host framework implements to turn a `Form` contract into transport output (the framework ships the Inertia renderer). |

Everything else in `ui` is self-contained: builders produce value objects that
serialize to the `PageContract` wire JSON; no host or transport is imported.

## Depending on `middag-io/ui` safely

- Depend only on `@api` types. If you need behaviour exposed only by an
  `@internal` symbol, open an issue to have it promoted rather than reaching in.
- **Default:** pin a caret range (`^1.0`) and read the CHANGELOG's **⚠ BREAKING
  CHANGES** section before crossing a minor — a `1.x` minor may carry a
  documented breaking `@api` change.
- **Zero-surprise upgrades:** pin a tilde patch range (for example `~1.2.1`) to
  receive only patches, and move across minors deliberately.
- The dependency direction only ever points downward: `ui` is the foundation and
  imports nothing from the rest of the stack; `framework` and the adapters
  depend on `ui`, never the reverse.
