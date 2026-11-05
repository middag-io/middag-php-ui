---
title: 'The first-party UI client is React + TypeScript + Inertia + ReUI'
status: accepted
date: 2026-04-16
last-updated: 2026-11-05
lang: en
domains: [ui, frontend]
deciders: [leadership]
decision: 'The first-party client, @middag-io/react, is React + TypeScript over Inertia with ReUI as its design system.'
related: [UI-001, UI-002]
supersedes: []
superseded_by: null
enforced_by:
  mdgstan: []
  docs: [docs/architecture.md]
---

# UI-003: The first-party UI client is React + TypeScript + Inertia + ReUI

## Context

UI-001 keeps the wire contract renderer-neutral, so any client may draw a page. MIDDAG still ships one client of its own, used by the Moodle plugin and the other hosts, and that client needs a framework, a language, a transport and a design system. The earlier client was Vue 3 + Inertia + PrimeVue + Pinia, chosen to align with Moodle core's internal Vue. It had no design system of the maturity MIDDAG needed, and keeping two client stacks doubles build, test and onboarding paths.

## Considered Options

1. Keep Vue 3 + Inertia + PrimeVue + Pinia; ReUI's copy-paste distribution needs React, and no Vue design system of comparable maturity existed.
2. Atlaskit; its license limits use to products that interoperate with Atlassian software.
3. Forge UI; its renderer only runs inside the Forge sandbox.
4. Run Vue and React side by side through a bridge layer; it keeps two stacks alive.
5. React + TypeScript over `@inertiajs/react`, with ReUI (shadcn/ui, Radix UI and Tailwind CSS, MIT, copy-paste distribution) as the design system, shipped as `@middag-io/react`. ← chosen

## Decision

The first-party client is `@middag-io/react`: React and TypeScript, with Inertia (`@inertiajs/react`) as the transport and ReUI as the design system foundation. Hosts consume the package; the Moodle plugin's `ui/` entry boots it with `createInertiaApp`. The choice is about the client only: the contracts of this package do not assume it, and another renderer can still consume the same wire (UI-001). Theme bridging to a host (for example Moodle color tokens to Tailwind tokens) belongs to the host adapter, not to the client or the contracts. There is no Vue/React bridge: Vue is no longer built or shipped, and screens not yet on React keep their legacy runtime until they move.

## Consequences

- (+) One client stack: one build, one test path and one onboarding path.
- (+) ReUI's copy-paste distribution puts the component source in the package, so MIDDAG owns and can change it; the MIT license puts no limit on where the product runs.
- (+) Inertia stayed the transport, so only the client target changed (`@inertiajs/vue3` to `@inertiajs/react`) and the server side did not move.
- (−) The client diverges from Moodle core's internal Vue; the product ships as an embedded app with its own UI identity instead of sharing components with Moodle core.
- (−) The existing Vue frontend has to be rewritten in full; moving one extension at a time spreads the cost instead of removing it.
- (−) The first-party client is coupled to Inertia, and some docblocks of this package name its mechanisms. Whether the `deferred` and `remember` block hints are renderer-neutral or an Inertia coupling stays open in UI-002; this record does not decide it.

## Enforcement

| Decision clause | Verification | State |
|---|---|---|
| Rationale, rejected alternatives and trade-offs | doc `docs-middag-dev:docs/framework/explanation/ui/architecture.md` | coded |
| First-party client and design system: current package and versions | doc `docs-middag-dev:docs/framework/reference/ui/design-system-catalog.md` | coded |
| ReUI registry preset and default theme | doc `middag-react:components.json`, doc `middag-react:docs/guides/theme.md` | coded |
| First-party client named next to the dependency graph; the contracts do not assume it | doc `docs/architecture.md` | coded |
