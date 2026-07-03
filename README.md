<div align="center">

# middag-io/ui

**Transport-agnostic PHP contract builders — describe a page once, render it anywhere.**

[Documentation](https://docs.middag.dev) · [What it does](#what-it-does) · [GitHub](https://github.com/middag-io/middag-php-ui)

[![CI](https://github.com/middag-io/middag-php-ui/actions/workflows/ci.yml/badge.svg)](https://github.com/middag-io/middag-php-ui/actions/workflows/ci.yml) [![License: Apache 2.0](https://img.shields.io/badge/License-Apache_2.0-blue.svg)](https://opensource.org/licenses/Apache-2.0) [![PHP](https://img.shields.io/badge/php-%5E8.2-777BB4.svg)](https://www.php.net/) [![PHPStan](https://img.shields.io/badge/PHPStan-level%206-brightgreen.svg)](https://phpstan.org/)

[![Packagist Version](https://img.shields.io/packagist/v/middag-io/ui.svg)](https://packagist.org/packages/middag-io/ui) [![Packagist Downloads](https://img.shields.io/packagist/dt/middag-io/ui.svg)](https://packagist.org/packages/middag-io/ui)

</div>

Transport-agnostic PHP contract builder system for MIDDAG's contract-driven UI. Produces `PageContract` (JSON) consumed by `@middag-io/react` via Inertia or any transport layer.

**Zero external dependencies.** PHP 8.2+ only.

**Open source — Apache-2.0.** The foundation layer of the MIDDAG framework stack (`ui` → `framework` → `moodle` / `wordpress` adapters; `@middag-io/react` on the client). Host-agnostic and transport-agnostic by design: this package knows nothing about Moodle, WordPress, or any transport — it only describes pages.

> [!NOTE]
> **Documentation** — the MIDDAG open-source project lives at **[middag.dev](https://middag.dev)**; full docs will be at **[docs.middag.dev](https://docs.middag.dev)** _(coming soon)_.
>
> Until the docs site is live, this README plus the `@api` docblocks in `src/` are the reference.

> [!IMPORTANT]
> **`0.6.0` — concern-first layout (BREAKING).** Every `Middag\Ui\*` FQN moved from the old stereotype-first layout (`Contract/ Data/ Builder/ Enum/ Schema/`) to **concern-first**: each concern (`Page`, `Form`, `Table`, `Block`, `Region`, `Action`, …) owns its interfaces + value objects + builders, with cross-cutting types under `Shared/{Data,Enum,Schema}`. The **wire JSON is unchanged** — only PHP namespaces moved. Consumers on `^0.5` must update imports (e.g. `Middag\Ui\Data\Fragment` → `Middag\Ui\Region\Fragment`, `Middag\Ui\Enum\FieldType` → `Middag\Ui\Shared\Enum\FieldType`). That release predated `1.0`, so no compatibility shims were shipped.

---

## What It Does

Pages in the MIDDAG stack are described by a `PageContract` — a JSON document declaring shell, page metadata, layout template, regions, and blocks. This library provides the PHP side: builders that produce that document. The React side (`@middag-io/react`) consumes it to render the actual UI.

This means PHP never renders HTML for pages — it declares structure, and React renders.

---

## Features

| Feature | What you get |
|---------|--------------|
| **Transport-agnostic contracts** | Builders produce `JsonSerializable` → `PageContract` (JSON). No Inertia/transport dependency; works with any wire. |
| **Zero dependencies** | PHP `^8.2` only. Consumers inherit no transitive packages. |
| **Host-agnostic** | No Moodle / WordPress / `mform` / capability coupling. Host-specifics live in the adapter, never here. |
| **3 levels of composition** | Convention (`CrudBuilder`) → convention + overrides → free composition (`PageBuilder`). |
| **CRUD convention builder** | `index`/`create`/`edit`/`show` pages from an entity class: i18n titles, filters, search, per-action `capability` gating. |
| **Block types** | `denseTable`, `formPanel`, `detailPanel`, `metricCard`, `emptyState`, `statusStrip`, `activityTimeline`, `markdownPanel`, `cardGrid`, `actionGrid`, `linkList`, `chart`, `tabs` — via `BlockBuilder` or the fluent `RegionBuilder`. |
| **Typed value objects** | `final readonly` + `JsonSerializable`. camelCase wire keys, omit-empty payloads, immutable witters. |
| **Partial fragments** | Server-push slices: `Fragment`, `RegionUpdate`, `ActionResult` (push + pull), `ResourcePatch`. |
| **Navigation tree** | 3-level `NavigationNode` (group / section / item), capability-filtered, drill-down + collapsible. |
| **Form system** | Contracts + VOs (`FieldDefinition`, `Condition`, `FormState`, `Section`, `Group`). Renderers live in adapters. |
| **i18n intents** | `Translatable` `{key, domain, params}`. The library never resolves translations — the client does. |
| **Quality gates** | Full test suite, high coverage, PHPStan L6, php-cs-fixer, Rector — enforced per commit. |

---

## Three Levels of Composition

### Level 1 — Convention (CrudBuilder)

Full CRUD pages from an entity class name:

```php
// index page with default columns, actions, pagination
$contract = PageBuilder::crud(Invoice::class)->build('index', [
    'rows' => $invoices,
    'pagination' => ['page' => 1, 'perPage' => 25, 'total' => 100, 'lastPage' => 4],
]);
```

### Level 2 — Convention + Overrides (CrudBuilder)

Customize columns, actions, layout without leaving the convention:

```php
$contract = CrudBuilder::for(Invoice::class, slug: 'invoices')
    ->without('show')
    ->columns(['number', 'status', 'amount', 'due_date'])
    ->column('status', fn (array &$col) => $col['variant'] = 'badge')
    ->filters([new FilterDefinition(key: 'status', label: 'Status')])
    ->searchable()                        // or mark a column searchable in its configurator
    ->sort('due_date', 'asc')
    ->perPage(50)
    ->i18n(domain: 'local_app')          // titles as i18n intent; falls back to literal nouns
    ->label('Invoice', 'Invoices')        // or override the noun explicitly
    ->build('index', ['rows' => $invoices]);
```

The class basename is treated as a **singular** noun. The library never
fabricates a plural: the slug defaults to the singular, and `for(class, slug:)`
overrides it for a plural URL. Titles resolve via `->label()` (explicit) →
`<key>_plural` i18n convention → singular fallback; create/edit verbs are
emitted as `crud_create`/`crud_edit` intents in a shared UI domain.

### Level 3 — Free Composition (PageBuilder)

Full control over every block and region:

```php
$contract = PageBuilder::page('invoices.show')
    ->title('Invoice #1234')
    ->subtitle('Due Jan 31')
    ->shell('product')
    ->layout('split')
    ->breadcrumbs(fn ($bc) => $bc->item('Invoices', '/invoices')->current('#1234'))
    ->actions([
        PageBuilder::action('pay', 'Mark Paid', ActionTarget::request('/invoices/1234/pay'), ActionIntent::PRIMARY),
    ])
    ->region('content', [
        BlockBuilder::detailPanel('invoice.detail', $sections),
    ])
    ->region('aside', [
        BlockBuilder::activityTimeline('invoice.activity', $groups),
    ])
    ->build();
```

---

## Inertia Props

When you need overlay or help panel metadata alongside the contract:

```php
return $this->inertia('Page', PageBuilder::page('orders.create')
    ->title('New Order')
    ->overlay()
    ->help('Creating an order', 'Fill in the details below.')
    ->inspector('/api/products/{id}')
    ->toProps());
// toProps() returns: ['contract' => ..., 'overlay' => true, 'help' => [...], 'inspector' => ...]
```

---

## Partial Fragments (server push)

A page can be served two ways, and both share one envelope contract (`ContractEnvelopeInterface`, carrying the same `version`):

1. **Full page in PHP** — `PageContract` declares the whole page (shell, layout, regions, blocks). The default.
2. **Page owned by the client, partial props from PHP** — when React owns the layout, the server returns a `Fragment`: one ready, self-describing slice of the contract (a block, a table, a region update, notifications) plus its routing `kind`. A fragment is a node of the contract with its own header, not a smaller page.

```php
use Middag\Ui\Region\Fragment;
use Middag\Ui\Region\RegionUpdate;

// after a filter/paginate, swap a region's content without reloading the page
$fragment = Fragment::region(RegionUpdate::replace('orders', $block1, $block2));
// {version: '1', kind: 'region', payload: {region: 'orders', mode: 'replace', blocks: [...]}}
```

`RegionUpdate` modes: `replace` / `append` / `prepend` / `remove` (by key) / `update` (match by key).

Mutations return an `ActionResult`, which carries both update strategies — push and pull:

```php
use Middag\Ui\Action\ActionResult;

return new ActionResult(
    fragments: [Fragment::table($tableConfig)],   // push: server already built the fresh piece
    refreshBlocks: ['sidebar'],                    // pull: client re-fetches these keys itself
);
```

A `ResourcePatch` rides along on a `Fragment` or `ActionResult` to push a partial change to preferences / capabilities / feature flags without resending the whole `PageResources`.

---

## Block Types

Static factories in `BlockBuilder::`:

| Method                                                                   | React Component        |
|--------------------------------------------------------------------------|------------------------|
| `BlockBuilder::denseTable($key, $columns, $rows)`                        | Dense data grid        |
| `BlockBuilder::formPanel($key, $action, $method, $schema, $values)`      | Form panel             |
| `BlockBuilder::detailPanel($key, $sections)`                             | Read-only detail view  |
| `BlockBuilder::metricCard($key, $value, $label, $delta, $icon, $href)`   | KPI card               |
| `BlockBuilder::emptyState($key, $variant, $description, $cta)`           | Empty state            |
| `BlockBuilder::statusStrip($key, $items, $tone)`                         | Status bar             |
| `BlockBuilder::activityTimeline($key, $groups, $hasMore, $loadMoreHref)` | Activity feed          |
| `BlockBuilder::markdownPanel($key, $content, $maxHeight)`                | Markdown body          |
| `BlockBuilder::cardGrid($key, $columns, $rows, $variant)`                | Card grid              |
| `BlockBuilder::actionGrid($key, $items, $flash)`                         | Action card grid       |
| `BlockBuilder::linkList($key, $items)`                                   | Link list              |
| `BlockBuilder::chart($key, $type, ChartSeries[], ...)`                   | Chart (ChartType enum) |
| `BlockBuilder::tabs($key, Tab[])`                                        | Tabs container         |

Or via `RegionBuilder` fluent API inside a `->region()` closure. Each method
mirrors its `BlockBuilder` factory one-to-one, so the same block type produces
an identical descriptor whichever entry point you use:

```php
->region('content', function ($r) {
    $r->metricCard('revenue', 42000, 'Revenue', delta: '+8%')
      ->denseTable('orders', ['id', 'total'], $rows)
      ->emptyState('no-results', variant: 'filtered');
})
```

---

## Navigation

`NavigationNode` is the `@api` value object for nav tree entries. Serializes to the shape consumed by `SidebarNav` in `@middag-io/react`:

```php
$node = new NavigationNode(
    key: 'audience.segments.index',
    label: 'Segments',
    icon: 'users',
    href: '/segments',
    active: true,
    weight: 10,
);
// Registered in AbstractNavigationRegistry implementations (in framework)
```

---

## Form System

This library provides contracts and value objects only — renderers live in `middag-io/framework` (Inertia) and the host adapters.

### Contracts

| Interface                | Role                                                             |
|--------------------------|------------------------------------------------------------------|
| `FormInterface`          | `schema()` → `hydrate()` → `validate()` → `validated()`          |
| `FieldInterface`         | `toDefinition(): FieldDefinition` — produces the boundary object |
| `FormRendererInterface`  | `target(): RenderTarget` + `render(Form): RendererOutput`        |
| `LayoutElementInterface` | `id()` + `children()` — Section and Group implement this         |

### Value Objects

| Class             | Notes                                                                                                                               |
|-------------------|-------------------------------------------------------------------------------------------------------------------------------------|
| `FieldDefinition` | Immutable boundary object between DSL and renderers. No `JsonSerializable` — renderers map manually.                                |
| `Condition`       | `field + operator (ConditionOperator enum) + value + kind`. Kinds: `visible_when`, `hidden_when`, `required_when`, `disabled_when`. |
| `FormState`       | Immutable readonly VO. `withValues()` / `withErrors()` return a new instance. Carries `values`, `errors`, `submitted`.              |
| `RendererOutput`  | Static factories `::html()` and `::props()` for the two render targets.                                                             |

### Layout Primitives

```php
$section = Section::of('personal')
    ->label(Translatable::of('personal_info_section', 'forms')) // or a literal string
    ->fields($nameField, $emailField, Group::of('phone')->fields($countryCode, $number));
```

`Section::label()` takes a `string|Translatable` like every other label in the
contract; `labelData()` serializes it via `Label` (a `{key, domain}` payload for
an intent, a raw string for a literal).

### Field Types (FieldType enum)

A closed backed enum of field types (`TEXT`, `TEXTAREA`, `SELECT`, `DATE`,
`RICHTEXT`, `TIME`, `AUTOCOMPLETE`, `TAGS`, …) — see `src/Shared/Enum/FieldType.php`
for the full catalogue.

Adding a type requires a new field class, matching renderer-side mappers, and the client component — most of which live downstream.

---

## Table Builder

Fluent API for producing `TableConfig` consumed by dense table blocks:

```php
$config = TableBuilder::make()
    ->column('name', 'Name', ['sortable' => true, 'searchable' => true])
    ->column('amount', 'Amount', ['sortable' => true, 'format' => ValueFormat::CURRENCY, 'formatOptions' => ['currency' => 'BRL']])
    ->filter('status', 'Status', FilterType::SELECT, [
        ['value' => 'active', 'label' => 'Active'],
        ['value' => 'inactive', 'label' => 'Inactive'],
    ])
    ->rowAction(PageBuilder::action('edit', 'Edit', ActionTarget::link('/invoices/{id}/edit')))
    ->bulkAction(PageBuilder::action('delete', 'Delete', ActionTarget::request('/invoices/bulk/delete'), ActionIntent::DANGER))
    ->options(new TableDisplayOptions(perPage: 25, sortColumn: 'name', selectable: true))
    ->build();
```

---

## Installation

Requires PHP `^8.2`. Install via Composer:

```bash
composer require middag-io/ui
```

---

## Development

```bash
composer install
composer test           # PHPUnit
composer check          # style + rector + stan + schema drift
composer fix            # style + rector (apply)
composer lint:php82     # parse every file (incl. tooling configs) on real PHP 8.2 (Docker)
```

The dev toolchain may run a newer PHP than the supported floor (`^8.2`). Newer
syntax such as `new X()->method()` parses on 8.4 but is a fatal error on 8.2, so
`composer check` alone will not catch it. Two guards keep the floor honest:
`composer lint:php82` runs `php -l` under a real PHP 8.2 interpreter over the
source, tests, and the `.php-cs-fixer.dist.php` / `.php-rector.php` configs; and
the CI **Static analysis & style** job runs entirely on PHP 8.2. PHPStan is
configured for the `8.2–8.4` range for version-sensitive type checks (it does
not catch syntax-level issues — that is the lint's job).

Git hooks configured automatically via `post-install-cmd`. `commit-msg` enforces Conventional Commits.

```
type(scope): description

Types: feat, fix, chore, docs, style, refactor, perf, test, build, ci, revert
```

Releases managed by [release-please](https://github.com/googleapis/release-please).

---

## License

Licensed under the Apache License, Version 2.0. See [`LICENSE`](LICENSE) and [`NOTICE`](NOTICE).

```
Copyright 2026 MIDDAG

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0
```

## Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md).
