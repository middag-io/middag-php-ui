---
id: UI-002
title: 'Page Contract & Declarative Composition Model'
status: accepted
date: 2026-04-16
lang: en
domains: [ui, frontend]
deciders: ['PENDING — original decider not recorded during the legacy-vault reconstruction; confirm with Michael Meneses before ratifying']
related: [UI-001]
supersedes: []
superseded_by: null
enforced_by:
  mdgstan: []
  docs: [framework/reference/ui/composition-levels-and-capabilities]
decision: 'Page UI is described via one closed, versioned page-contract value object (shell/page/layout/regions/blocks), consumed through 4 progressive composition levels (L1-L4) over the same wire format.'
---

# UI-002: Page Contract & Declarative Composition Model

> [!NOTE]
> **Provenance.** Reconstructed from the `moodle-local_middag` legacy vault (`ADR-807`, sections 1, 3-5, 7, 9-16, 19-25 — the sections that are a generic, reapplicable mechanism rather than a product-specific decision). Sections 2 and 6 of the original restated UI-001 and are not duplicated here. Theme bridging and build-pipeline sections (17-18) belong to the consuming host adapter's decisions record. The applied visual identity and the `product` shell instance are a downstream product concern, not part of this library's contract.

## Context

Need: describe a MIDDAG product page once and render it across a spectrum from fully generated (no hand-written React) to a fully bespoke page, without forcing every screen into either extreme or maintaining two incompatible wire formats for "simple" vs "custom" pages.

## Considered Options

1. **Two separate wire formats** — one for simple/generated pages, one for fully custom pages — rejected: doubles the maintenance surface and forces every screen to commit to an extreme upfront.
2. **A single rigid generation path** (CRUD-only, no escape hatch) — rejected: cannot express bespoke composition when the convention doesn't fit.
3. **One closed page-contract value object, consumed through progressive composition levels over the same wire format** ← chosen.

## Decision

The **page contract** is a closed, versioned, `JsonSerializable` value object composed of five layers:

| Layer     | Responsibility                                                                             |
|-----------|--------------------------------------------------------------------------------------------|
| `shell`   | Macro frame of the experience (`product`, `admin`, `course`, `immersive`)                  |
| `page`    | Screen identity — title, breadcrumbs, global actions                                       |
| `layout`  | Structural template (`stack`, `split`, `dashboard`, `master-detail`, `wizard`*, `canvas`*) |
| `regions` | Named slots (`header`, `sidebar`, `content`, `aside`, `footer`)                            |
| `blocks`  | Typed descriptors of renderable widgets                                                    |

The backend describes intent, data, and permitted actions; the frontend owns materialization into React components via a closed, versioned block registry. The contract, registry, and React layer are **internal by default** — promotion to a stable `@api` extension surface requires real usage by at least two concrete consumers plus a stabilized shell/layout/block taxonomy (see the OSS boundary rule in `CLAUDE.md`).

Four composition levels via `page_builder`, same wire contract regardless of path (L1 convention-only through L4 fully dedicated React page) — see `framework/reference/ui/composition-levels-and-capabilities` (in `docs-middag-dev`) for the level-by-level API table, the block catalog, and the v2 capability rollout.

Table and form are **first-class blocks**, not bespoke per-screen code: the `Table` concern drives server-side sort/filter/pagination via Inertia partial reloads; the `Form` concern bridges the form DSL to the contract (see `middag-php-framework`'s form decisions for the DSL itself). Extensions register custom block types via `block_type_interface`; the same contract transports standard and custom blocks identically. Navigation is a **registry** (`Navigation` concern), not a host callback — host-level callback patterns (e.g. Moodle's `get_quick_access_links()`) are deprecated in favor of registering against this contract; see the consuming adapter's own decisions record for the host-side migration.

## Consequences

- One wire format serves all four composition tiers — no separate "simple mode" data shape to maintain in parallel.
- Closed-by-default defers premature `@api` commitments until real multi-consumer usage exists.
- Two layout kinds (`wizard`, `canvas`) are reserved in the schema but unimplemented — avoids a future breaking change at the cost of carrying dead types today.
- Host-specific rendering, HTTP transport, and theme bridging stay downstream (per the OSS boundary rule) in `middag-php-framework` and host adapters — this contract makes no assumption about any of the three.
- Full capability rollout detail (polling, conditional row actions, confirmation dialogs, toasts, rich column variants) lives in the reference doc above, not in this ADR — it changes independently of the durable contract shape.

## Enforcement

| Decision clause | Verification | State |
|---|---|---|
| Page contract stays closed by default (no `@api` promotion without ≥2 real consumers + a stabilized taxonomy) | OSS boundary rule, `CLAUDE.md` | **coded** |
| Composition levels L1-L4, block catalog, v2 capabilities (polling, confirmations, toasts, rich columns) | doc `framework/reference/ui/composition-levels-and-capabilities` | **coded** |
