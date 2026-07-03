# middag-io/ui

Transport-agnostic PHP **contract builders** for contract-driven UI. Describe a page once — its
regions, forms, tables, blocks, navigation and actions — as an immutable value object, then let any
host or adapter render it. The library produces a JSON **page contract**; it never produces HTML.

> Describe the page once. Render it anywhere.

Apache-2.0. On the `1.x` line a minor release may carry a documented breaking change, cut
deliberately by a maintainer — strict semver (breaking only in majors) starts at `2.0`.

## Technical documentation

- **[Architecture](architecture.md)** — the PageContract system, the three composition levels, the
  concern-first layout, and the OSS boundary (why nothing here renders). Start here.

## Quick start

Requires PHP `^8.2`. Install via Composer:

```bash
composer require middag-io/ui
```

Compose a form layout from immutable value objects — the result serializes to a JSON contract a
renderer consumes:

```php
use Middag\Ui\Block\Section;
use Middag\Ui\Form\Group;

$section = Section::of('personal')
    ->label('Personal details')
    ->fields(
        $nameField,
        $emailField,
        Group::of('phone')->fields($countryField, $numberField),
    );
```

The builders carry no transport and no host knowledge: the same contract renders standalone, inside a
Moodle plugin, or inside WordPress, by swapping the renderer downstream.

## Where it is consumed

| Consumer | How |
|----------|-----|
| **`middag-io/framework`** | Maps the contracts to its Inertia renderer and form pipeline. |
| **Host adapters** (Moodle, WordPress) | Render the contract through the host's UI, via the framework. |
| **`@middag-io/react`** (NPM) | Consumes the emitted JSON page contract on the client. |

## Open source and MIDDAG

`middag-io/ui`, `middag-io/framework`, and the Moodle/WordPress adapters are open source under
Apache-2.0 — the generic plumbing, free, forever. The governed domain infrastructure built on top is
MIDDAG's proprietary product, opt-in; this library has zero dependencies and never points at it.

## Contributing

See [`CONTRIBUTING.md`](../CONTRIBUTING.md) for the workflow, coding standards and quality gates.
