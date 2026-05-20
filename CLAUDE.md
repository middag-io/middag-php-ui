# CLAUDE.md — middag-io/ui

## O que é este pacote

Biblioteca PHP de contract builders para UI contract-driven. Produz PageContracts (JSON) consumidos por `@middag-io/react` via InertiaJS ou qualquer transport.

- **Transport-agnostic** — builders produzem `JsonSerializable` arrays, não dependem de Inertia
- **Zero dependências** — apenas PHP ^8.2
- **3 níveis de composição** (ADR-807): L1 (CrudBuilder convention), L2 (CrudBuilder + overrides), L3 (PageBuilder composição livre)

## Estrutura

| Directório | Conteúdo |
|------------|----------|
| `src/Contract/` | Interfaces (`*Interface.php`) — todas estendem `JsonSerializable` |
| `src/Builder/` | Implementações fluent (PageBuilder, CrudBuilder, RegionBuilder, Block, BreadcrumbListBuilder) |
| `src/Data/` | Readonly value objects (PageContractData, PageMeta, BlockDescriptor, etc.) |
| `src/Infrastructure/Form/` | Form contracts + AbstractForm + Field types + Layout (Group, Section) + FormValidator |
| `tests/` | PHPUnit tests |

**Nota:** `InertiaRenderer` + `InertiaFieldMapper` movidos para `middag-io/framework` em B-146 (PD-008 C). ui hospeda apenas contracts e Layout primitives.

## Comandos

| Comando | O que faz |
|---------|-----------|
| `composer test` | PHPUnit |
| `composer analyse` | PHPStan (quando configurado) |

## Convenções

- **PHP 8.2+** — readonly classes, constructor promotion, match expressions
- **PSR-4** — namespace `Middag\Ui\`
- **Strict types** — `declare(strict_types=1)` em todos os ficheiros
- **Fluent builders** — métodos retornam `static` para chaining
- **JsonSerializable** — todas as data classes implementam serialização JSON
- **Sem backward compat** — breaking changes são aceitáveis pre-1.0

## Relação com outros pacotes

```
middag-io/ui (este)         ← zero deps
middag-io/framework         ← requires middag-io/ui
middag-io/moodle            ← requires middag-io/framework
middag-io/wordpress         ← requires middag-io/framework
@middag-io/react (NPM)     ← consome o JSON produzido por este pacote
```
