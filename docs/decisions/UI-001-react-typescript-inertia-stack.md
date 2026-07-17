---
id: UI-001
title: 'Adopt React + TypeScript + Inertia as the Sole Frontend Stack'
status: accepted
date: 2026-04-16
domains: [ui, frontend]
related: [UI-002]
supersedes: []
superseded_by: null
lang: en
---

# UI-001: Adopt React + TypeScript + Inertia as the Sole Frontend Stack

> [!NOTE]
> **Provenance.** Reconstructed from the `moodle-local_middag` legacy vault (`ADR-107`, decided 2026-04-16 in the NV-05 PRD session). This is an archaeology pass, not a new decision — dates and rationale are historical.

## Context

The pre-v5.0 stack (Vue 3 + Inertia + PrimeVue + Pinia) was chosen for alignment with Moodle core, which uses Vue internally. It blocked adoption of **ReUI** (shadcn/ui + Radix UI + Tailwind CSS v4), the design system selected for the v5.0 rewrite: shadcn's copy-paste distribution model requires React as the runtime, and no Vue equivalent exists at comparable enterprise maturity.

Two alternatives were evaluated and rejected:

- **Atlaskit** (Atlassian) — its ADG license restricts use to products that interoperate with Atlassian software; a Moodle plugin does not qualify.
- **Forge UI** (`@forge/react`) — uses a proprietary, non-DOM renderer that only runs inside the Forge sandbox.

## Decision

React 18 + TypeScript + `@inertiajs/react` is the sole frontend stack. No Vue/React coexistence layer — migration is incremental, extension-by-extension; legacy Vue routes keep working until rewritten. Inertia stays the transport protocol; only the frontend target changed (`@inertiajs/vue3` → `@inertiajs/react`). ReUI (MIT, copy-paste) is the design system foundation — see REF-UI-001-01 for the component catalog and licensing detail.

## Consequences

- Single stack removes the cost of dual build/test/onboarding paths.
- ~60-75% smaller bundle than Atlaskit-class alternatives (REF-UI-001-01).
- Diverges from Moodle core's internal Vue usage — accepted: the product ships as an embedded app with its own UI identity, not shared components with Moodle core.
- Requires a full rewrite of the existing Vue frontend — mitigated by incremental, per-extension migration rather than a big-bang cutover.

## Out of scope

- The declarative page-composition model this stack enables (page contract, page builder, block registry) — see [UI-002](./UI-002-page-contract-composition-model.md).
- Host-specific theme bridging (e.g. Moodle color tokens → Tailwind tokens) — lives in the consuming host adapter's own decisions record, not here.

## Links

- [REF-UI-001-01 — Design System Catalog & Licensing Rationale](../ref/REF-UI-001-01-design-system-catalog.md)
- [UI-002 — Page Contract & Declarative Composition Model](./UI-002-page-contract-composition-model.md)
- [architecture.md](../architecture.md) — current implementation
