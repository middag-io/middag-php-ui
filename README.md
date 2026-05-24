# middag-io/ui

[![License: Apache 2.0](https://img.shields.io/badge/License-Apache_2.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)
[![PHP](https://img.shields.io/badge/php-%5E8.2-777BB4.svg)](https://www.php.net/)

Transport-agnostic PHP contract builder system for MIDDAG's contract-driven UI. Produces `PageContractData` (JSON) consumed by `@middag-io/react` via Inertia or any transport layer.

**Zero external dependencies.** PHP 8.2+ only.

---

## What It Does

Pages in the MIDDAG stack are described by a `PageContractData` — a JSON document declaring shell, page metadata, layout template, regions, and blocks. This library provides the PHP side: builders that produce that document. The React side (`@middag-io/react`) consumes it to render the actual UI.

This means PHP never renders HTML for pages — it declares structure, and React renders.

---

## Three Levels of Composition (ADR-807)

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
$contract = PageBuilder::crud(Invoice::class)
    ->without('show')
    ->columns(['number', 'status', 'amount', 'due_date'])
    ->column('status', fn (array &$col) => $col['variant'] = 'badge')
    ->sort('due_date', 'asc')
    ->per_page(50)
    ->build('index', ['rows' => $invoices]);
```

### Level 3 — Free Composition (PageBuilder)

Full control over every block and region:

```php
$contract = PageBuilder::page('invoices.show')
    ->title('Invoice #1234')
    ->subtitle('Due Jan 31')
    ->shell('product')
    ->layout('split')
    ->breadcrumbs(fn ($bc) => $bc->item('Invoices', '/invoices')->current('#1234'))
    ->actions([PageBuilder::action('pay', 'Mark Paid', 'primary', '/invoices/1234/pay', 'post')])
    ->region('content', [
        Block::detail_panel('invoice.detail', $sections),
    ])
    ->region('aside', [
        Block::activity_timeline('invoice.activity', $groups),
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
    ->to_props());
// to_props() returns: ['contract' => ..., 'overlay' => true, 'help' => [...], 'inspector' => ...]
```

---

## Block Types

Static factories in `Block::`:

| Method | React Component |
|---|---|
| `Block::dense_table($key, $columns, $rows)` | Dense data grid |
| `Block::form_panel($key, $action, $method, $schema, $values)` | Form panel |
| `Block::detail_panel($key, $sections)` | Read-only detail view |
| `Block::metric_card($key, $value, $label, $delta, $icon, $href)` | KPI card |
| `Block::empty_state($key, $variant, $description, $cta)` | Empty state |
| `Block::status_strip($key, $items, $tone)` | Status bar |
| `Block::activity_timeline($key, $groups, $has_more, $load_more_href)` | Activity feed |
| `Block::markdown_panel($key, $content, $max_height)` | Markdown body |
| `Block::card_grid($key, $columns, $rows, $variant)` | Card grid |
| `Block::action_grid($key, $items, $flash)` | Action card grid |
| `Block::link_list($key, $items)` | Link list |

Or via `RegionBuilder` fluent API inside a `->region()` closure:

```php
->region('content', function ($r) {
    $r->metric_card('revenue', 'Revenue', ['value' => 42000])
      ->dense_table('orders', 'Orders', ['rows' => $rows])
      ->empty_state('no-results', ['variant' => 'filtered']);
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

The form system follows ADR-806. This library provides contracts and value objects only — renderers live in `middag-io/framework` (Inertia) and `middag-io/moodle` (mform).

### Contracts

| Interface | Role |
|---|---|
| `FormInterface` | `schema()` → `hydrate()` → `validate()` → `validated()` |
| `FieldInterface` | `to_definition(): FieldDefinition` — produces the boundary object |
| `FormRendererInterface` | `target(): RenderTarget` + `render(Form): RendererOutput` |
| `ConditionInterface` | `to_condition(): Condition` |
| `LayoutElementInterface` | `id()` + `children()` — Section and Group implement this |

### Value Objects

| Class | Notes |
|---|---|
| `FieldDefinition` | Immutable boundary object between DSL and renderers. No `JsonSerializable` — renderers map manually. |
| `Condition` | `field + operator (ConditionOperator enum) + value + kind`. Kinds: `visible_when`, `hidden_when`, `required_when`, `disabled_when`. |
| `FormState` | Mutable via clone-based `with_values()` / `with_errors()`. Carries `values`, `errors`, `submitted`. |
| `RendererOutput` | Static factories `::html()` and `::props()` for the two render targets. |

### Layout Primitives

```php
$section = Section::of('personal')
    ->label('personal_info_section')
    ->fields($nameField, $emailField, Group::of('phone')->fields($countryCode, $number));
```

### Field Types (FieldType enum)

20 types: `TEXT`, `TEXTAREA`, `PASSWORD`, `EMAIL`, `URL`, `INT`, `FLOAT`, `SELECT`, `MULTISELECT`, `RADIO`, `CHECKBOX`, `SWITCH`, `DATE`, `DATETIME`, `DURATION`, `FILE`, `ENTITY_PICKER`, `HIDDEN`, `STATIC`, `HEADER`.

Adding a type requires: ADR amendment + field class + `MformFieldMapper` case + `InertiaFieldMapper` case + Vue component.

---

## Table Builder

Fluent API for producing `TableConfig` consumed by dense table blocks:

```php
$config = TableBuilder::make()
    ->column('name', 'Name', ['sortable' => true, 'searchable' => true])
    ->column('status', 'Status', ['type' => 'select', 'options' => ['active' => 'Active']])
    ->column('amount', 'Amount', ['sortable' => true, 'type' => 'number'])
    ->filter('status', 'Status', 'select', ['active' => 'Active', 'inactive' => 'Inactive'])
    ->action('export', 'Export CSV', 'download')
    ->with_options(['defaultSort' => 'name', 'defaultSortDir' => 'asc'])
    ->build();
```

---

## CRUD Convention Resolver

Static helpers for deriving CRUD conventions from entity class names:

```php
CrudConventionResolver::slug(Invoice::class);        // 'invoices'
CrudConventionResolver::title(Invoice::class);       // 'Invoices'
CrudConventionResolver::singular(Invoice::class);    // 'Invoice'
CrudConventionResolver::columns(Invoice::class);     // public props excl. id, timestamps
CrudConventionResolver::capability(Invoice::class);  // 'local/middag:manage_invoice'
```

---

## Installation

Add the MIDDAG Private Satis repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://privatesatis.middag.io"
        }
    ]
}
```

```bash
composer require middag-io/ui
```

---

## Development

```bash
composer install
composer test           # PHPUnit
composer check          # style + rector + stan
composer fix            # style + rector (apply)
```

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
