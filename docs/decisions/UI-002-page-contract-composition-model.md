---
id: UI-002
title: 'Page Contract & Declarative Composition Model'
status: accepted
date: 2026-04-16
domains: [ui, frontend]
related: [UI-001]
supersedes: []
superseded_by: null
lang: en
---

# UI-002: Page Contract & Declarative Composition Model

> [!NOTE]
> **Provenance.** Reconstructed from the `moodle-local_middag` legacy vault (`ADR-807`, sections 1, 3-5, 7, 9-16, 19-25 — the sections that are a generic, reapplicable mechanism rather than a product-specific decision). Sections 2 and 6 of the original restated UI-001 and are not duplicated here. Theme bridging and build-pipeline sections (17-18) belong to the consuming host adapter's decisions record. The applied visual identity and the `product` shell instance are a downstream product concern, not part of this library's contract.

## Context

Need: describe a MIDDAG product page once and render it across a spectrum from fully generated (no hand-written React) to a fully bespoke page, without forcing every screen into either extreme or maintaining two incompatible wire formats for "simple" vs "custom" pages.

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

Three composition levels via `page_builder`, same wire contract regardless of path:

| Level | API                                      | Use                               |
|-------|------------------------------------------|-----------------------------------|
| L1    | `page_builder::crud(Entity::class)`      | Standard CRUD, minimal config     |
| L2    | `page_builder::crud(...)->without(...)`  | CRUD with adjustments             |
| L3    | `page_builder::page('key')->region(...)` | Custom pages composed from blocks |
| L4    | `inertia::render('Page', $props)`        | Fully dedicated React page        |

Table and form are **first-class blocks**, not bespoke per-screen code: the `Table` concern drives server-side sort/filter/pagination via Inertia partial reloads; the `Form` concern bridges the form DSL to the contract (see `middag-php-framework`'s form decisions for the DSL itself). Extensions register custom block types via `block_type_interface`; the same contract transports standard and custom blocks identically. Navigation is a **registry** (`Navigation` concern), not a host callback — host-level callback patterns (e.g. Moodle's `get_quick_access_links()`) are deprecated in favor of registering against this contract; see the consuming adapter's own decisions record for the host-side migration.

## Consequences

- One wire format serves all four composition tiers — no separate "simple mode" data shape to maintain in parallel.
- Closed-by-default defers premature `@api` commitments until real multi-consumer usage exists.
- Two layout kinds (`wizard`, `canvas`) are reserved in the schema but unimplemented — avoids a future breaking change at the cost of carrying dead types today.

## Out of scope

- Host-specific rendering, HTTP transport, and theme bridging — per the OSS boundary rule, those live downstream in `middag-php-framework` and host adapters.
- Capability rollout detail (polling, conditional row actions, confirmation dialogs, toasts, rich column variants) — see REF-UI-002-01.

## Links

- [REF-UI-002-01 — Composition Levels, Block Catalog & v2 Capability Rollout](../ref/REF-UI-002-01-composition-and-capabilities.md)
- [UI-001 — Adopt React + TypeScript + Inertia as the Sole Frontend Stack](./UI-001-react-typescript-inertia-stack.md)
- [architecture.md](../architecture.md) — current implementation
