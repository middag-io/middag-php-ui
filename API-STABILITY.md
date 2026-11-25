# API Stability

This document defines what is public and supported in `middag-io/ui` from
`2.0.0`, so downstream packages — `middag-io/framework` (which implements the
`Form` render seam), the OSS adapters (`middag-io/moodle`,
`middag-io/wordpress`), the `@middag-io/react` client that consumes the wire
JSON, and any proprietary consumer built on top — can depend on the contract
builders without guessing.

The support rules of the whole library family (lifecycle, what each release
may carry, the compatibility matrix) are summarized here and in
<https://docs.middag.dev/libraries/reference/versioning-and-compatibility>.
This file lists the public surface of `middag-io/ui`.

`middag-io/ui` is the **foundation layer** of the MIDDAG stack (`ui` →
`framework` → the Moodle/WordPress adapters). It has **zero external
dependencies** and knows nothing about any host or transport: it only describes
pages as a `PageContract` (JSON).

## Lifecycle

- **`2.0.0` is the first supported release.** Support starts there.
- **The `1.x` line is pre-stable and unsupported.** It carries no compatibility
  promise, no deprecation cycle, no backports and no security fixes.
- **The cut to `2.0.0` is clean.** There is no upgrade path from `1.x`; the
  `CHANGELOG` continues across the cut.

## Stability levels

Every type carries a class-level annotation that states its stability:

| Annotation | Meaning |
|---|---|
| `@api` | **Public, supported surface.** You may implement, extend, type-hint, instantiate and catch these. Changes follow [Versioning](#versioning-from-200): a patch carries fixes only, a minor is additive, and a break ships only in a MAJOR, following [Deprecation](#deprecation). The builders are the exception: see [Builders are a call surface](#builders-are-a-call-surface). |
| `@internal` | **Implementation detail.** Outside the support promise. May change or be removed in any release, including patches. Do not depend on these from outside the package. |

Support covers the `@api` surface only. Anything untagged, or tagged
`@internal`, is outside the promise. Every type in `src/` carries exactly one
of the two tags.

`NavigationRegistryInterface` is an implementable contract: its method signatures, including parameter names, are frozen for 2.x. It is not a call surface like the builders, so it gains no method or parameter in a 2.x MINOR. Registration methods take `string|Translatable` labels and the optional `entityType`, `badgeVariant`, `statusColor` (all levels) and `target`, `href` (items) that `NavigationNode` carries. Labels are never translated by the registry; a `Translatable` reaches the client as `{key, domain, params?}`. An item has either a route or an external `href`, never both, and `href` takes no `routeParams`; `href` must be an absolute URL starting with lowercase `http://` or `https://`. Implementations must use the interface's parameter names, so named arguments work against any implementation.

## The public surface

The public surface is the set of `@api`-annotated types: the interfaces under
each concern's `Contract/` sub-namespace (`Page/Contract/`, `Form/Contract/`,
`Table/Contract/`, `Block/Contract/`, `Region/Contract/`, `Action/Contract/`,
`Navigation/Contract/`, `Inspector/Contract/`, `Envelope/Contract/`,
`EditablePanel/Contract/`), the concern builders and their value objects
(including `Condition\Condition` and `EditablePanel\EditablePanelDescriptor`), and the cross-cutting types under
`Shared/`, except the two `Shared/Concerns/` traits.

`Shared/Concerns/ProvidesJsonSchema`, `Shared/Concerns/AssertsStringList` and everything under `Schema/` (the JSON
Schema registry) are `@internal`: they support schema export inside the
package and are not part of the public surface.

### Builders are a call surface

`Block\BlockBuilder`, `Region\RegionBuilder` and `Region\Contract\RegionBuilderInterface` are `@api` for calling: type-hint them, instantiate `RegionBuilder` and call their methods. They are not for implementing. From `2.0.0`, adding a method, or adding an optional parameter to an existing method, is a MINOR change for these three types. If your code implements `RegionBuilderInterface`, a MINOR may break it. Removing a method or parameter, renaming one, reordering parameters or making one required is still a MAJOR change. Named arguments are part of the call surface, so parameter names follow the same rule.

You may extend `BlockBuilder` or `RegionBuilder` only without declaring or overriding members; a subclass that declares a method may fail to load after a MINOR that adds or extends a method of the same name. The classes stay non-final so that empty pass-through subclasses keep working.

## The wire contract

Beyond the PHP surface, `middag-io/ui` has a second, stronger compatibility
contract: the **`PageContract` wire JSON** emitted for `@middag-io/react`. The
JSON shape is the real integration boundary with the client. A PHP-level rename
that leaves the emitted JSON unchanged is not a wire break; a change to the JSON
shape is, and follows the same rules as a break of the `@api` surface.

The wire contract has two root bundles under `schema/`: `page-contract.json` (the `PageContract` envelope, with `fragment.json` for the `Fragment` envelope) and `shared-props.json`, whose root is `SharedProps`, the props shared with every page. Its slots are `auth`, `theme`, `flash`, `errors`, `csrf_token`, `locale`, `scope` and `navigation`. The root is open (`additionalProperties: true`), so hosts and products can add their own keys such as `middag.*`; the typed slots stay strict. All bundles carry the same `$defs` and the same `x-contract-version`.

The shared props types are `@api`: `Shared\ValueObject\SharedProps`, `SharedAuth`, `SharedTheme` and `FlashMessages`, and `Navigation\NavigationTree` (the return type of `NavigationRegistryInterface::build()`). Identity, capabilities, theme and locale travel in the shared props only.

The `errors` slot carries the validation errors of the current request: a `FormErrors` map or, when the request carried the `X-Inertia-Error-Bag` header, a `FormErrorBags` map of bag name to `FormErrors`. The schema accepts either (`anyOf`), and this shape is frozen for 2.x. The two shapes can overlap on the wire (an empty object, or a bag whose fields are named like a `FieldMessage`), so the wire does not say which one it is: a client that sent `X-Inertia-Error-Bag` reads `errors[bag]`, and every other client reads the map as flat. On the PHP side the type (`FormErrors|FormErrorBags`) is unambiguous.

Each value in a `FormErrors` map is a `FieldError`: one message, a list of messages or structured messages, or one structured message. The structured message is `Form\FieldMessage` (`@api`), serialized as `{message, key, domain, params?}`: the server-resolved `message` is the fallback, and `key`, `domain` and `params` let the client translate it. `params` is omitted when empty. A structured message given as its array form (for example rebuilt from the session) is serialized through `FieldMessage` too, so the same rules apply to it. `FormErrors::jsonSerialize()` and `FormErrorBags::jsonSerialize()` return `stdClass`, so a map always encodes as a JSON object, even with numeric field names.

Adding an optional key or a new `$def` is additive and ships in a MINOR; clients ignore keys they do not know. Adding a new discriminator value (`kind`, `type`) is additive and ships in a MINOR; clients treat an unknown value as unsupported and degrade that element instead of rejecting the whole payload. Enum values follow the policy in [Enum evolution on the wire](#enum-evolution-on-the-wire). Removing or renaming a key, removing an enum value, changing a type or making a key required is a breaking change: it ships in a MAJOR and raises `ContractEnvelopeInterface::VERSION` and `x-contract-version`. `@middag-io/ui-contracts` mirrors `schema/`, ships the TypeScript types and zod schemas generated from it, and versions on its own line.

### Enum evolution on the wire

Not every enum can grow in a MINOR. It depends on what happens to an unknown value.

**Control enums** drive behavior, so the contract rejects an unknown value instead of guessing. Adding a value to one is a **MAJOR** change:

- `HttpMethod` (also the form `submitMethod`)
- `ActionTargetKind`
- `FragmentKind`
- `RegionUpdateMode`
- `RenderTarget`
- `ConditionOperator` (the condition `operator`)
- `documentScope`

**Open enums** reach the client renderer, which falls back to a safe default for a value it does not know. Adding a value to one is a **MINOR** change. Removing or renaming a value is a **MAJOR** change:

- `ActionIntent`
- `NotificationLevel`
- `ThemeMode`
- `ValueFormat`
- `ColumnVariant`
- `FilterType`
- `ChartType`
- `FormComponent`
- `FieldType`
- `CardGridVariant` (the `card_grid` `data.variant`)

Removing or renaming a value of a control enum is a MAJOR change too.

The `form_panel` block takes its schema nodes as `FormSchemaNodeInterface` value objects. A loose array raises a `TypeError`.

### Block data shapes

The `data` of a block stays an open object in `BlockDescriptor`, and the block builders keep taking plain arrays. Alongside, the schema carries one `$def` per block payload that `@middag-io/react` renders, so clients can generate types for them. These are schema-only classes (no instances, like `FormCondition`), all `@api`:

| Block `type` | Payload `$def` | Nested `$defs` |
|---|---|---|
| `dense_table` | `Block\DenseTableBlockData` | `Block\DenseTableColumnDef`, `Block\DenseTablePagination`, `Block\DenseTableSort`, `Block\DenseTableFilterDef`, `Block\EntityRef`; cell values `Block\RichStatusValue`, `Block\AnnotatedValue`, `Block\ProgressValue`, `Block\LinkGroupItem` |
| `metric_card` | `Block\MetricCardBlockData` | |
| `empty_state` | `Block\EmptyStateBlockData` | |
| `status_strip` | `Block\StatusStripBlockData` | `Block\StatusStripItem` |
| `detail_panel` | `Block\DetailPanelBlockData` | `Block\DetailPanelSectionDef`, `Block\DetailPanelFieldDef` |
| `activity_timeline` | `Block\ActivityTimelineBlockData` | `Block\ActivityTimelineEntry` |
| `markdown_panel` | `Block\MarkdownPanelBlockData` | |
| `card_grid` | `Block\CardGridBlockData` | `Block\CardGridColumnDef`, `Shared\Enum\CardGridVariant` |
| `link_list` | `Block\LinkListBlockData` | `Block\LinkListItem` |
| `chart` | `Block\ChartBlockData` | `Block\ChartSeries` |
| `tabs` | `Block\TabsBlockData` | `Page\Tab` |
| `action_grid` | `Block\ActionGridBlockData` | `Block\ActionGridItem` |

Shared by several payloads: `Block\EmptyStateDef` (the `emptyState` key) and `Action\ConditionalAction` (row actions: an `Action` plus `variant`, `visible_when`, `disabled_when`, `loading_when` and `disabled_reason_field`; a plain `Action` is a valid `ConditionalAction`).

These `$defs` follow the wire rules above: adding an optional key or a `$def` is a MINOR change; removing a key or making one required is a MAJOR change. `DenseTableColumnDef.variant` takes a `ColumnVariant` value or any other string.

The builders take the optional keys of these payloads as trailing optional parameters: `metricCard(deltaDirection:)`, `emptyState(ctaSecondary:)`, `statusStrip(score:)` and `activityTimeline(limit:)`, on `BlockBuilder`, `RegionBuilder` and `RegionBuilderInterface`.

### Page help and inspector

`PageContract` has two optional slots: `help` (`{title, body, learnMore?}`) and `inspector` (an `InspectorDescriptor`). `PageBuilder::build()` fills them from `help()` and `inspector()`, so a page built with `build()` alone keeps them. `PageBuilder::toProps()` still returns them as sibling props next to `contract`, unchanged.

### Page builder factories

`PageBuilder::page()` and `CrudBuilder::for()` instantiate the called class, so an empty pass-through subclass gets an instance of itself. Their constructors are `protected`.

## Versioning from `2.0.0`

From `2.0.0`, the open-source `middag-php-*` libraries (`ui`, `framework`, `moodle`, `wordpress` and the `demo-standalone` example) share the MAJOR version. MINOR and PATCH releases are per library. Each library requires its siblings with `^2.N`, where `N` is the lowest MINOR it needs, so Composer installs a compatible set. Require each package you import with `^2.0`, or with the MINOR whose features you use.

A PATCH carries fixes only. A MINOR carries additive `@api` changes. A MAJOR carries breaking `@api` changes, following [Deprecation](#deprecation).

A platform floor (the lowest PHP version or the lowest Symfony line) rises only in a MAJOR. Support for a new PHP version or a new Symfony LTS line is added in a MINOR.

The next MAJOR (`3.0`) drops the PHP versions that no longer receive security support.

## Deprecation

A removal or incompatible change of an `@api` element ships only in a MAJOR. Where possible it is announced first in a MINOR of the current MAJOR: the element carries `@deprecated` naming its replacement, the changelog lists it, and code that still runs emits an `E_USER_DEPRECATED` notice where PHP can raise one. A change that cannot be deprecated in place, such as a new abstract method on an interface or a changed signature, ships in a MAJOR with an entry in `UPGRADING.md`. This does not apply to the builders in [Builders are a call surface](#builders-are-a-call-surface): there, a new method or a new optional parameter ships in a MINOR.

## Support

Fixes go to the latest MINOR of the current MAJOR, as PATCH releases. When the next MAJOR ships, the last MINOR of the previous MAJOR receives critical and high severity security fixes only, from an `N.x` branch opened at the cut, for 6 months or until the end of life of the PHP or host version that motivated the new MAJOR, whichever comes first. Releases before `2.0.0` are unsupported.

## Releases and contributing

Releases are cut by release-please from Conventional Commits. The contributor rules, the version check included, are in [`CONTRIBUTING.md`](CONTRIBUTING.md).

## The cross-package seam

`middag-io/ui` defines the contract and `middag-io/framework` provides the
runtime implementation of one seam:

| Contract | Role |
|---|---|
| `Middag\Ui\Form\Contract\FormRendererInterface` | The render seam a host framework implements to turn a `Form` contract into transport output (the framework ships the Inertia renderer). |

Everything else in `ui` is self-contained: builders produce value objects that
serialize to the `PageContract` wire JSON; no host or transport is imported.

## Depending on `middag-io/ui` safely

- Depend only on `@api` types. If you need behaviour exposed only by an
  `@internal` symbol, open an issue to have it promoted rather than reaching in.
  A promotion is an additive change that ships in a MINOR.
- Require `middag-io/ui` with `^2.0`, or with the MINOR whose features you use.
- The dependency direction only ever points downward: `ui` is the foundation and
  imports nothing from the rest of the stack; `framework` and the adapters
  depend on `ui`, never the reverse.
