# API Stability

This document defines what is public and supported in `middag-io/ui` from
`2.0.0`, so downstream packages — `middag-io/framework` (which implements the
`Form` render seam), the OSS adapters (`middag-io/moodle`,
`middag-io/wordpress`), the `@middag-io/react` client that consumes the wire
JSON, and any proprietary consumer built on top — can depend on the contract
builders without guessing.

The support rules of the whole library family (lifecycle, what each release
may carry, the compatibility matrix) live in one place:
<https://docs.middag.dev/libraries/reference/versioning-and-compatibility>.
This file lists the public surface of `middag-io/ui` and points there for
everything else.

`middag-io/ui` is the **foundation layer** of the MIDDAG stack (`ui` →
`framework` → the Moodle/WordPress adapters). It has **zero external
dependencies** and knows nothing about any host or transport: it only describes
pages as a `PageContract` (JSON).

## Lifecycle

- **`2.0.0` is the first supported release.** Support starts there.
- **The `1.x` line is pre-stable and unsupported.** It carries no compatibility
  promise, no deprecation cycle, no backports and no security fixes.
- **The cut to `2.0.0` is clean.** There is no upgrade path from `1.x`; the
  `CHANGELOG` continues across the cut.

## Stability levels

Every type carries a class-level annotation that states its stability:

| Annotation | Meaning |
|---|---|
| `@api` | **Public, supported surface.** You may implement, extend, type-hint, instantiate and catch these. Changes follow the family rules: a patch carries fixes only, a minor never breaks, and a break ships only in a family MAJOR after a deprecation in a minor. |
| `@internal` | **Implementation detail.** Outside the support promise. May change or be removed in any release, including patches. Do not depend on these from outside the package. |

Support covers the `@api` surface only. Anything untagged, or tagged
`@internal`, is outside the promise. Every type in `src/` carries exactly one
of the two tags.

## The public surface

The public surface is the set of `@api`-annotated types: the interfaces under
each concern's `Contract/` sub-namespace (`Page/Contract/`, `Form/Contract/`,
`Table/Contract/`, `Block/Contract/`, `Region/Contract/`, `Action/Contract/`,
`Navigation/Contract/`, `Inspector/Contract/`, `Envelope/Contract/`,
`EditablePanel/Contract/`), the concern builders and their value objects
(including `Condition\Condition` and `EditablePanel\EditablePanelDescriptor`), and the cross-cutting types under
`Shared/`, except `Shared/Concerns/ProvidesJsonSchema`.

`Shared/Concerns/ProvidesJsonSchema` and everything under `Schema/` (the JSON
Schema registry) are `@internal`: they support schema export inside the
package and are not part of the public surface.

## The wire contract

Beyond the PHP surface, `middag-io/ui` has a second, stronger compatibility
contract: the **`PageContract` wire JSON** emitted for `@middag-io/react`. The
JSON shape is the real integration boundary with the client. A PHP-level rename
that leaves the emitted JSON unchanged is not a wire break; a change to the JSON
shape is, and follows the same rules as a break of the `@api` surface.

## The version family

From `2.0.0`, `middag-io/ui` is one of the `middag-php-*` libraries that form
one version family. They share MAJOR.MINOR; PATCH is per library and carries
fixes only. A family MINOR or MAJOR is released in every library, decided by
the product owner. No library cuts a MAJOR on its own.

**Family rule for consumers:** from `2.0.0`, every library of the family is
required with the same `^2.N` range. If you depend on `middag-io/ui` and on
`middag-io/framework`, both carry the same `^2.N`.

Releases are cut by
[release-please](https://github.com/googleapis/release-please) from
[Conventional Commits](https://www.conventionalcommits.org/); the commit rules
and the version family check are in [`CONTRIBUTING.md`](CONTRIBUTING.md)
§Versioning.

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
  A promotion is an additive change for a family MINOR.
- Require `middag-io/ui` with the same `^2.N` as every other `middag-io/*`
  library of the family you depend on.
- The dependency direction only ever points downward: `ui` is the foundation and
  imports nothing from the rest of the stack; `framework` and the adapters
  depend on `ui`, never the reverse.
