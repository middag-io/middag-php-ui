---
ref: REF-UI-001-01
adr: UI-001
title: 'Design System Catalog & Licensing Rationale'
lang: en
---

# REF-UI-001-01: Design System Catalog & Licensing Rationale

> Detail supporting [UI-001](../decisions/UI-001-react-typescript-inertia-stack.md). Reconstructed from the `moodle-local_middag` legacy vault (ADR-807 §6, ADR-107).

## Component catalog

- **~52+ shadcn/ui base components**, copy-paste distributed (MIT) — code becomes project-owned on copy, no runtime dependency on an external package.
- **17 custom ReUI primitives** layered on top of the shadcn/Radix base for product-specific composition needs.
- **TanStack Table v8** — powers the `Table` concern's Data Grid rendering (server-side sort/filter/pagination via Inertia partial reloads; see UI-002).

## Bundle size comparison

| Stack                                  | Approx. gzip size |
|----------------------------------------|-------------------|
| Atlaskit (Atlassian)                   | ~350-500 KB       |
| ReUI (shadcn/ui + Radix + Tailwind v4) | ~165 KB           |

~60-75% reduction versus the Atlaskit-class alternative that was evaluated and rejected on licensing grounds (see UI-001 Context).

## Licensing detail

- **Atlaskit / ADG (Atlassian Design Guidelines) license** — restricts use to products that interoperate with Atlassian software. A standalone Moodle plugin does not qualify; ruled out regardless of technical fit.
- **Forge UI (`@forge/react`)** — ships a proprietary, non-DOM renderer that only executes inside the Forge sandbox runtime. Not usable outside an Atlassian Forge app.
- **ReUI / shadcn/ui** — MIT licensed, copy-paste distribution model. Components are copied into the consuming project's own source tree at adoption time, becoming project-owned code with zero ongoing vendor/runtime dependency. This was the deciding factor over a conventional npm-dependency design system.

## Ecosystem alignment note

The prior stack (Vue 3 + PrimeVue + Pinia) had been chosen specifically for alignment with Moodle core's internal use of Vue. Adopting React diverges from that alignment — accepted as a tradeoff because the product operates as an embedded app with its own UI identity, not a set of components shared with Moodle core rendering.
