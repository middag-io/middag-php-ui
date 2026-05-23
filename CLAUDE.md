# CLAUDE.md — middag-io/ui

## O que é este pacote

Biblioteca PHP de contract builders para UI contract-driven. Produz `PageContractData` (JSON) consumido por `@middag-io/react` via InertiaJS ou qualquer transport.

- **Transport-agnostic** — builders produzem `JsonSerializable` arrays, sem dep em Inertia
- **Zero dependências** — apenas PHP ^8.2
- **3 níveis de composição** (ADR-807): L1 CrudBuilder convention, L2 CrudBuilder com overrides, L3 PageBuilder composição livre
- **Lib fechada** — NÃO adicionar novas features sem ADR. Consumers são framework, moodle, wordpress.

## Estrutura de Diretórios

```
src/
  AbstractPage.php          — base para páginas roteáveis (implementa PageInterface)
  AbstractWidget.php        — base para widgets descobertos por tag no container

  Contract/                 — interfaces @api e value objects de contrato
    PageContractInterface   — extends JsonSerializable; contrato raiz da lib
    PageInterface           — marker para service discovery por slug
    NavigationNode          — readonly class com jsonSerialize() (ADR-807 nav tree)
    BlockDescriptorInterface, BlockInterface, BreadcrumbInterface, ...
    Form/
      FieldInterface        — to_definition(): FieldDefinition
      FormInterface         — schema() → hydrate() → validate() → validated()
      FormRendererInterface — target(): RenderTarget + render(Form): RendererOutput
      ConditionInterface    — to_condition(): Condition
      LayoutElementInterface — id() + children()

  Builder/                  — fluent builders (todos @internal)
    PageBuilder             — entry point principal; factory crud(), page(), action()
    CrudBuilder             — L1/L2 convenção sobre entity class
    RegionBuilder           — compõe blocks dentro de uma região
    Block                   — static factory de BlockDescriptors por tipo
    BreadcrumbListBuilder   — constrói lista de Breadcrumb

  Data/                     — readonly value objects (sem deps externas)
    PageContractData        — raiz: shell + PageMeta + LayoutDescriptor
    PageMeta                — key, title, subtitle, breadcrumbs, actions
    LayoutDescriptor        — template + regions (Map<string, BlockDescriptor[]>)
    BlockDescriptor         — type + key + data (array) + variant + actions + meta
    PageAction              — id + label + intent + href + method + icon + flags
    PageResources           — auth + capabilities + feature_flags + locale
    Breadcrumb              — label + href + external
    InspectorDescriptor     — endpoint + width
    Table/
      Column                — readonly class: key, label, sortable, searchable, type, options
      TableConfig           — readonly class: Column[] + filters + actions + options
    Form/
      Condition             — readonly: field + operator (ConditionOperator) + value + kind
      FieldDefinition       — readonly boundary object entre DSL e renderers; SEM JsonSerializable
      FormState             — mutable (clone-based): values + errors + submitted
      RendererOutput        — readonly: target + body + props; factories html() e props()

  Enum/
    FieldType               — catálogo fechado de 20 tipos de campo (ADR-806)
    ConditionOperator       — 13 operadores; is_mform_compatible() filtra MATCHES
    RenderTarget            — MFORM | INERTIA

  Infrastructure/
    Form/
      Layout/
        Group               — layout_element_interface: id + fields(...)
        Section             — layout_element_interface: id + label + fields(...)

  Support/
    CrudControllerSupport   — static helpers: index/create/edit/show → PageContractData
    CrudConventionResolver  — static: slug/title/singular/columns/form_class/capability

  Widget/
    TableBuilder            — fluent: column/filter/action/with_options → TableConfig
```

## Invariantes de Design

| Regra | Motivo |
|---|---|
| `middag-php-ui` tem zero deps externas | Consumers (framework, moodle) não herdam transitividade indesejada |
| Todos os Data/ são `readonly class` | Imutabilidade garantida em compile time — PHP 8.2 |
| `FieldDefinition` e `Condition` NÃO implementam `JsonSerializable` | São boundary objects; renderers (InertiaFieldMapper, MformFieldMapper) mapeiam manualmente — evita acoplamento ao formato de wire |
| `FormState` é mutable por clone | Imita value object com `with_*` pattern; `is_submitted()` depende do ciclo hydrate → validate |
| Renderers (InertiaRenderer, InertiaFieldMapper, MformRenderer, MformFieldMapper) vivem em `middag-io/framework` e `middag-io/moodle` | ui hospeda apenas contratos e layout primitives — B-146/PD-008 C |
| `NavigationNode` está em `Contract/` (não `Data/`) | É um value object @api com jsonSerialize(); não é um builder interno |

## Comandos

```bash
composer test          # PHPUnit
composer check:style   # php-cs-fixer dry-run
composer check:rector  # rector dry-run
composer check:stan    # phpstan
composer check         # os três acima em sequência
composer fix:style     # php-cs-fixer apply
composer fix:rector    # rector apply
```

## Convenções

- PHP 8.2+ — `readonly class`, constructor promotion, `match`, enums backed
- PSR-4 — namespace raiz `Middag\Ui\`
- `declare(strict_types=1)` em todos os arquivos
- Fluent builders retornam `static` para chaining
- Commits convencionais: `feat:`, `fix:`, `refactor:`, `test:`, `chore:`, `docs:`
- NÃO adicionar backward compat shims — lib pre-1.0, breaking é aceitável
- NÃO mover lógica de renderer/mapper para cá — fica em framework/moodle

## Relação com outros pacotes

```
middag-io/ui (este)         ← zero deps
  └─ middag-io/framework    ← require ui; hospeda InertiaRenderer, InertiaFieldMapper,
  │                            AbstractValidator, StandaloneKernel, ServiceProvider
  │    ├─ middag-io/moodle  ← require framework; hospeda MformRenderer, MformFieldMapper,
  │    │                       HttpClientAdapter, SignalDispatcher (Moodle)
  │    └─ middag-io/wordpress ← require framework; hospeda adapters WP
  └─ @middag-io/react (NPM) ← consome o JSON produzido por este pacote
```

## Testes

```bash
composer test
```

PHPUnit 11+. Coverage por CoversClass. Estrutura espelha `src/`:

```
tests/
  Builder/            — Block, BreadcrumbListBuilder, CrudBuilder, PageBuilder, RegionBuilder
  Contract/           — NavigationNode
  Data/
    Form/             — Condition, FieldDefinition, FormState, RendererOutput
    Table/            — Column, TableConfig
    BlockDescriptor, Breadcrumb, InspectorDescriptor, LayoutDescriptor,
    PageAction, PageContractData, PageMeta, PageResources
  Enum/               — ConditionOperator, FieldType
  Infrastructure/
    Form/Layout/      — Group, Section
  Support/            — CrudControllerSupport, CrudConventionResolver
  Widget/             — TableBuilder
```
