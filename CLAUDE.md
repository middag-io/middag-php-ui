# CLAUDE.md — middag-io/ui

## O que é este pacote

Biblioteca PHP de contract builders para UI contract-driven. Produz um contrato de página (`JsonSerializable` → JSON) consumido por `@middag-io/react` via InertiaJS ou qualquer transport.

- **Transport-agnostic** — builders produzem `JsonSerializable`, sem dep de Inertia/transport
- **Zero dependências** — apenas PHP `^8.2`
- **Host-agnostic** — NÃO conhece Moodle, WordPress nem nenhum host. Sem `mform`, `wpdb`, capabilities, nomes de coluna ou convenções de plugin. Qualquer coisa específica de host vive no adapter consumidor, nunca aqui.
- **Lib fechada** — não adicionar features sem ADR.
- **3 níveis de composição** (ADR-807): L1 convenção, L2 convenção + overrides, L3 composição livre.

## Organização (eixo: concern-first)

Pacote pequeno e single-purpose, organizado por **concern de UI** na raiz de `src/` (ex.: `Action/ Block/ Page/ Table/ Navigation/ Region/ Form/ Inspector/ Shared/ ...`). Não há `Contract/` central — cada papel convive co-localizado dentro da concern:

- **interfaces `@api`** vivem no próprio diretório da concern (sufixo `Interface`); nada concreto numa interface
- **builders fluentes** retornam `static` e produzem value objects
- **value objects** são `readonly` (serializáveis quando vão pro wire). Um VO pareado com um `*Interface` dedicado pode ser `readonly` não-`final` por design (seam de extensão que o adapter implementa); VO-folha leva `final readonly`
- **enums backed** (catálogos fechados), VOs e helpers transversais ficam em `Shared/` (`Shared/Enum/`, `Shared/Data/`, `Shared/Schema/`)

`src/` (raiz) hospeda os entrypoints do contrato de página + o envelope raiz.

## Invariantes de Design

| Regra                                                                      | Motivo                                                                                                                                                                                                            |
|----------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Zero deps externas                                                         | Consumers não herdam transitividade indesejada                                                                                                                                                                    |
| `Data/` são `readonly class`                                               | Imutabilidade garantida em compile time                                                                                                                                                                           |
| Sem vazamento de host (Moodle/WordPress/mform/etc.)                        | Lib agnóstica; host-specifics ficam no adapter                                                                                                                                                                    |
| Renderers e field-mappers vivem em `framework`/adapters, não aqui          | ui hospeda só contratos + layout primitives                                                                                                                                                                       |
| Nenhum contrato do ui produz HTML (`render(): string`)                     | Render é de produto/host → vive em core/adapter (ex.: `BlockInterface`/`DashboardWidgetInterface` foram movidos pro core). Contratos do ui produzem **dados/VOs** (ex.: `FormRendererInterface`→`RendererOutput`) |
| `FieldDefinition` e `Condition` NÃO implementam `JsonSerializable`         | São boundary objects; renderers mapeiam — evita acoplar ao formato de wire                                                                                                                                        |
| Interfaces `@api` co-localizadas na concern; VO concreto fora de interface | Eixo concern-first (não há `Contract/` central)                                                                                                                                                                   |

> **Autz é dado, não chamada:** tokens opacos de autorização (ex.: o campo `capability` em `Action`/`NavigationNode`/`CrudBuilder`) são **dados** permitidos no contrato — o proibido é **chamar API de host** (`has_capability`/`mform`/`wpdb`). O adapter resolve o token; o ui só o carrega.

## Comandos

```bash
composer test          # PHPUnit
composer test:coverage # PHPUnit + cobertura (requer xdebug/pcov)
composer check:style   # php-cs-fixer dry-run
composer check:rector  # rector dry-run
composer check:stan    # phpstan
composer check         # os três checks em sequência
composer fix:style     # php-cs-fixer apply
composer fix:rector    # rector apply
```

## Convenções

- PHP 8.2+ — `readonly class`, constructor promotion, `match`, enums backed
- PSR-4 — namespace raiz `Middag\Ui\`; `declare(strict_types=1)` em todos os arquivos
- Cobertura: todo teste declara `#[CoversClass]`; `tests/` espelha `src/`
- Fluent builders retornam `static`
- Commits convencionais; pré-1.0 → breaking aceitável, sem shims de compat
- NÃO mover lógica de renderer/mapper nem qualquer host-specific pra cá

## Relação com outros pacotes

```
middag-io/ui (this repo)        ← zero deps, host-agnostic
  └─ middag-io/framework        ← require ui; renderers/kernel genéricos
       ├─ middag-io/moodle      ← require framework; adapters + host-specifics Moodle
       └─ middag-io/wordpress   ← require framework; adapters WordPress
  └─ @middag-io/react (NPM)     ← consome o JSON produzido por este pacote
```
