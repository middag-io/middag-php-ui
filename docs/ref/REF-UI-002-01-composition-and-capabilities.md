---
ref: REF-UI-002-01
adr: UI-002
title: 'Composition Levels, Block Catalog & v2 Capability Rollout'
lang: en
---

# REF-UI-002-01: Composition Levels, Block Catalog & v2 Capability Rollout

> Detail supporting [UI-002](../decisions/UI-002-page-contract-composition-model.md). Reconstructed from the `moodle-local_middag` legacy vault (ADR-807 §§9-25).

## `page_builder` — progressive composition

A convenience layer that produces a standard `page_contract` — the frontend consumes the same contract regardless of whether a builder generated it or it was assembled by hand. L1 conventions (naming, entity introspection) derive form fields, table columns, actions, routes, and permissions automatically; each convention accepts an explicit override. Historically exposed as a generated CLI proxy (`base/ui/page_builder.php`) over the underlying `framework/shared/ui/page_builder.php` implementation — this generation detail is host/product tooling, not part of this library's contract.

## Low-touch ↔ high-touch spectrum

Not every screen needs to be 100% declarative. Rule of thumb: use the dynamic contract for shell/layout/regions/reusable blocks; drop to a dedicated component when the generic abstraction would reduce clarity or raise maintenance cost.

| Extreme    | Description                         | Controller                     | Frontend                       |
|------------|-------------------------------------|--------------------------------|--------------------------------|
| Low-touch  | Generated from PHP, no manual React | `page_builder`                 | Renders via registries         |
| Mixed      | Builder + custom blocks             | `page_builder` + custom blocks | Registries + custom components |
| High-touch | Dedicated React page                | `inertia::render()`            | Purpose-built React page       |

The same screen may combine standard and custom blocks in a single composition — the contract does not distinguish origin.

## Block extensibility

Extensions register custom block types by implementing `block_type_interface` (PHP) and registering the matching React component in the client-side `blockRegistry`. Standard blocks live in `base/blocks/`; custom blocks live per-extension. Dashboards compose via `page_builder` level 3 (developer-defined blocks/regions/layout in PHP); an admin-configurable persistence mode (DB-backed layout editing) was explicitly deferred to a future milestone, reusing the same block infrastructure.

## Table and form as first-class blocks

- **`dense_table`** — server-side sort/filter/paginate abstraction (fluent API: columns, data source, pagination, sort, filters, actions). React side renders via TanStack Table v8, dispatching through Inertia partial reloads rather than full page loads.
- **`form_panel`** — bridges the form field DSL to the page contract; the form renderer serializes the field schema, and `form_panel` wraps the output as a block usable in any layout. CRUD pages generated via `page_builder` automatically produce a `stack` layout with a `form_panel` in the content region. Client side: `@inertiajs/react`'s `useForm()`, React Hook Form + Zod for client-side validation, ReUI/shadcn form fields.

## Reserved layouts

`wizard` (steps/content/actions — multi-step forms, onboarding flows) and `canvas` (toolbar/canvas/inspector — visual builder UIs) are reserved in both the TypeScript types and the PHP value objects, but no React component implements them yet. The reservation exists to avoid a breaking schema change whenever they are implemented.

## v2 capabilities

- **Polling (Inertia-native).** Blocks declare `block.meta.polling` (`interval`, `enabled`); the server remains the source of truth, refresh cycles run through `router.reload({ only: ['contract'] })`. No client-side `stopWhen`, no SSE/WebSockets. Client hook: `usePolling()`.
- **Conditional row actions.** `visible_when` / `disabled_when` / `loading_when` / `disabled_reason_field`, using the same `FormCondition` model as the form DSL. Complex logic is evaluated server-side as derived fields (`_`-prefixed convention); the client only evaluates simple conditions via a shared `evaluateCondition()` used by both the form-panel block and the dense-table block.
- **Unified confirmation dialogs.** `ActionConfirmation` (title/message with `{field}`-style interpolation, intent, confirm/cancel labels, `waitForPolling`, `waitingMessage`) shared between row actions and page actions via a generic `ConfirmationDialog` component.
- **Toast notifications.** Two channels converge on one renderer (Sonner): a server flash channel (`flash.toast` via Inertia, rendered automatically by a `FlashProvider`) and an imperative client hook (`useToast()`).
- **Rich column variants.** `rich_status` (colored badge — preferred for new work), `html` (`dangerouslySetInnerHTML` with sanitization — discouraged for new development), `link_group` (conditional icon-button group).
- **Page actions with side effects.** `page_action` supports in-place POST/PUT/DELETE without navigation (`router[method]()` with `preserveState: true`), button-level loading state, and reuses `ActionConfirmation`. Superseded the earlier inline `ActionButton`, keeping backward compatibility for `requiresConfirmation`.
