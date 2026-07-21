---
id: UI-001
title: 'Adopt React + TypeScript + Inertia as the Sole Frontend Stack'
status: accepted
date: 2026-04-16
lang: en
domains: [ui, frontend]
deciders: ['PENDING — original decider not recorded during the legacy-vault reconstruction; confirm with Michael Meneses before ratifying']
related: [UI-002]
supersedes: []
superseded_by: null
enforced_by:
  mdgstan: []
  docs: [framework/reference/ui/design-system-catalog]
decision: 'React 19 + TypeScript + Inertia, with ReUI (shadcn/Radix/Tailwind, MIT copy-paste) as the design system, is the sole frontend stack — no Vue coexistence.'
---

# UI-001: Adopt React + TypeScript + Inertia as the Sole Frontend Stack

> [!NOTE]
> **Provenance.** Reconstructed from the `moodle-local_middag` legacy vault (`ADR-107`, decided 2026-04-16 in the NV-05 PRD session). This is an archaeology pass, not a new decision — dates and rationale are historical.

## Context

The pre-v5.0 stack (Vue 3 + Inertia + PrimeVue + Pinia) was chosen for alignment with Moodle core, which uses Vue internally. It blocked adoption of **ReUI** (shadcn/ui + Radix UI + Tailwind CSS v4), the design system selected for the v5.0 rewrite: shadcn's copy-paste distribution model requires React as the runtime, and no Vue equivalent exists at comparable enterprise maturity.

## Considered Options

1. **Keep the status quo** (Vue 3 + Inertia + PrimeVue + Pinia) — rejected: blocks ReUI adoption, the design system this rewrite needs.
2. **Atlaskit** (Atlassian) — rejected: its ADG license restricts use to products that interoperate with Atlassian software; a Moodle plugin does not qualify.
3. **Forge UI** (`@forge/react`) — rejected: uses a proprietary, non-DOM renderer that only runs inside the Forge sandbox.
4. **React + TypeScript + `@inertiajs/react` + ReUI** ← chosen.

## Decision

React (19 as of this reformat — the ecosystem was on React 18 at the original 2026-04-16 decision date, per `@middag-io/react`'s current `package.json`; the major-version bump doesn't change the decision itself) + TypeScript + `@inertiajs/react` is the sole frontend stack. No Vue/React coexistence layer — migration is incremental, extension-by-extension; legacy Vue routes keep working until rewritten. Inertia stays the transport protocol; only the frontend target changed (`@inertiajs/vue3` → `@inertiajs/react`). ReUI (MIT, copy-paste) is the design system foundation — see `framework/reference/ui/design-system-catalog` (in `docs-middag-dev`) for the component catalog and licensing detail.

## Consequences

- Single stack removes the cost of dual build/test/onboarding paths.
- ~60-75% smaller bundle than Atlaskit-class alternatives (see the reference doc above).
- Diverges from Moodle core's internal Vue usage — accepted: the product ships as an embedded app with its own UI identity, not shared components with Moodle core.
- Requires a full rewrite of the existing Vue frontend — mitigated by incremental, per-extension migration rather than a big-bang cutover.
- The declarative page-composition model this stack enables (page contract, page builder, block registry) is a separate decision — see [UI-002](./UI-002-page-contract-composition-model.md). Host-specific theme bridging (e.g. Moodle color tokens → Tailwind tokens) lives in the consuming host adapter's own decisions record, not here.

## Enforcement

| Decision clause | Verification | State |
|---|---|---|
| ReUI (MIT, copy-paste) as design-system foundation; component catalog + bundle-size rationale | doc `framework/reference/ui/design-system-catalog` | **coded** |
| No Vue/React coexistence layer (migration incremental, per-extension) | no automated check | **planned** |
