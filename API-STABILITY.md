# API Stability

This document defines what is public and supported in `middag-io/ui`, and how
the public surface may evolve on the current **`1.x`** line and from `2.0.0`, so downstream
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
shape is, and is governed by the same versioning policy below.

## How releases are cut

Releases are cut **exclusively** by
[release-please](https://github.com/googleapis/release-please) from
[Conventional Commits](https://www.conventionalcommits.org/). There are no
manual tags. The versioning rules come from
ADR-032 of the internal `docs-middag-ops`
(version family of the `middag-php-*` libraries), which amends ADR-003 for the
libraries.

## The `1.x` policy

On `1.x` the commit type drives the bump (`fix:` → patch, `feat:` → minor):

- **Patch** (`1.y.Z`) — bug fixes and `@internal`-only changes. Never a breaking
  `@api` (or wire) change.
- **Minor** (`1.Y.0`) — additive `@api` changes (new builders, new optional
  parameters, promoting an `@internal` symbol to `@api`). Until **2026-11-30** a
  minor may also carry a breaking `@api` change, under a recorded exception
  that ends on that date. That exception is not renewed: from **2026-12-01**, if the family has not reached `2.0.0`, no
  break lands on `1.x`.
- **How a `1.x` break is written.** As a `feat:` commit (so it cuts a minor,
  never a patch) with the break described in the commit body; never as `fix:`,
  `refactor:` or `chore:`. Never use `!` or a
  `BREAKING CHANGE:` footer on `1.x`: release-please would cut `2.0.0` in this
  lib alone, and the version family check fails the PR.
- **No `2.0.0` from this lib alone.** `2.0.0` is cut for the whole family at
  once, on the decision of the product owner.

> Historical note: `1.2.1` shipped the audit-consolidation breaking change (the
> `TableOptions` → `TableDisplayOptions` rename) as a patch by explicit
> maintainer decision, closing the OSS audit before external consumers existed.
> The `0.6.0` concern-first layout move (every `Middag\Ui\*` FQN relocated)
> predated `1.0`, so shipped without shims. From this document on, a breaking
> `@api` change never lands in a patch.

## From `2.0.0`: the version family

From `2.0.0`, all `middag-php-*` libraries (`framework`, `core`, `ui`,
`moodle`, `wordpress`, `licensing`, `dev-tools`, `demo-standalone`) form one
version family:

- **Shared MAJOR.MINOR.** Every lib carries the same MAJOR.MINOR; PATCH is per
  lib. A family MINOR or MAJOR is released in every lib, including the ones
  that did not change.
- **Patch** — fixes only. A feature never ships in a patch.
- **Minor** — new features, released only in a family MINOR. A minor never
  breaks: a removal is first deprecated in a minor, with a runtime deprecation
  notice, and removed only in the next family MAJOR.
- **Major** — breaking changes, released only in a family MAJOR. Each family
  MAJOR publishes a compatibility matrix (Symfony, PHP, Moodle, WordPress).
- **Release automation.** release-please cuts only patches (`versioning:
  always-bump-patch`, set at the `2.0.0` cut). A family MINOR or MAJOR is a
  coordinated `Release-As: X.Y.0` commit in every lib.
- **Who decides.** The product owner decides every family MINOR (when a new
  Moodle version needs support, a MIDDAG release needs a feature, a deprecation
  or a new range, or a new Symfony minor needs absorbing) and every family
  MAJOR.
- **Previous MAJOR.** The last minor of a family MAJOR receives security fixes
  only, for 6 months after the next MAJOR ships.

## The version family check

Every PR runs the `php-version-family-check / check` job
(`.github/workflows/family-window.yml`). It reads the PR title and every
non-merge commit of the PR. The family window is the organization variable
`PHP_LIBS_OPEN_RELEASE`, set and cleared on the order of the product owner:
empty means closed, `X.Y` (with Y > 0) is a family MINOR window, `X.0` is a
family MAJOR window.

- `!` or a `BREAKING CHANGE:` footer fails unless a MAJOR window is open.
- `Release-As:` fails unless a window is open and the footer names that
  window's `X.Y.0`.
- `feat` fails unless a window is open, but only once this lib is on `2.0.0`
  or later (read from `.release-please-manifest.json` on the base branch). On
  `1.x`, `feat` passes.

To pass with a feature on `2.x`, keep the PR open until the product owner opens
a family MINOR window. Keep it current by merging `main` into it (never rebase
or force-push), then re-run the check once the window is open.

- A window counts as open only when it is ahead of this lib's current version
  and the PR targets `main`; a value left set after its family release reads
  as closed.
- While a window is open, this lib's Release PR is merged only at the
  coordinated `X.Y.0`.
- A re-run reads the base of the original event. After the `2.0.0` cut, a PR
  opened before the cut must merge `main` into it so that the check reads the
  new version.

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
- **On `1.x`:** pin a caret range (`^1.0`) and read the commits of a minor
  before crossing it, since a `1.x` minor released up to 2026-11-30 may carry a
  breaking `@api` change described in a commit body. To receive only
  patches, pin a tilde patch range (for example `~1.2.1`) and move across
  minors deliberately.
- **From `2.0.0`:** declare one `^2.N` range, the same for every
  `middag-io/*` sibling you depend on. Minor upgrades inside that range do not
  break.
- The dependency direction only ever points downward: `ui` is the foundation and
  imports nothing from the rest of the stack; `framework` and the adapters
  depend on `ui`, never the reverse.
