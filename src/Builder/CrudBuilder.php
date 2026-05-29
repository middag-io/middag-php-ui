<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Builder;

use Closure;
use InvalidArgumentException;
use Middag\Ui\Contract\ActionInterface;
use Middag\Ui\Contract\CrudBuilderInterface;
use Middag\Ui\Data\Action;
use Middag\Ui\Data\ActionTarget;
use Middag\Ui\Data\BlockDescriptor;
use Middag\Ui\Data\Column;
use Middag\Ui\Data\Confirmation;
use Middag\Ui\Data\Pagination;
use Middag\Ui\Data\TableConfig;
use Middag\Ui\Data\TableOptions;
use Middag\Ui\Data\Translatable;
use Middag\Ui\Enum\ActionIntent;
use Middag\Ui\Enum\HttpMethod;
use Middag\Ui\Enum\ValueFormat;
use Middag\Ui\PageContract;

/**
 * CRUD convention builder (ADR-807 Levels 1-2).
 *
 * Generates PageContract instances for index/create/edit/show actions
 * following convention-over-configuration. Override methods allow point-of-use
 * customization without dropping to Level 3 composable.
 *
 * @api
 */
class CrudBuilder implements CrudBuilderInterface
{
    /** Default i18n domain for the generic CRUD verb keys (owned by the client UI layer). */
    private const VERB_DOMAIN = 'ui';

    private readonly string $slug;

    /** Singular, lower-cased entity basename used for singular titles. */
    private readonly string $singular;

    /** @var string[] CRUD actions to generate */
    private array $actions = ['index', 'create', 'edit', 'show'];

    /** @var null|string[] Explicit columns (null = convention) */
    private ?array $columnsList = null;

    /** @var array<string, Closure> Column configurators */
    private array $columnConfigs = [];

    /** @var null|string[] Row actions (null = default ['edit', 'delete']) */
    private ?array $rowActionsList = null;

    /** @var null|string[] Bulk actions */
    private ?array $bulkActionsList = null;

    /** @var null|ActionInterface[] Page actions (null = default ['create']) */
    private ?array $pageActionsList = null;

    private int $perPage = 25;

    private string $sortColumn = 'created_at';

    private string $sortDirection = 'desc';

    /** Entity-noun i18n domain; null = no i18n (literal-string fallback). */
    private ?string $i18nDomain = null;

    /** i18n domain for the generic verb keys (crud_create/crud_edit). */
    private string $verbDomain = self::VERB_DOMAIN;

    /** Explicit singular label override (string literal or i18n intent). */
    private string|Translatable|null $labelSingular = null;

    /** Explicit plural label override; null = derive (i18n `_plural` or singular fallback). */
    private string|Translatable|null $labelPlural = null;

    private ?string $customLayout = null;

    private function __construct(private readonly string $entityClass, ?string $slug = null)
    {
        // The class basename is treated as a SINGULAR noun. The library never
        // fabricates a plural (impossible without a locale inflector): the slug
        // defaults to the singular and the caller overrides it for plural URLs.
        $parts = explode('\\', $this->entityClass);
        $basename = end($parts);
        $this->singular = strtolower($basename);
        $this->slug = $slug ?? $this->singular;
    }

    public static function for(string $entityClass, ?string $slug = null): self
    {
        return new self($entityClass, $slug);
    }

    // --- Level 2 Override API ---

    /**
     * Remove specific CRUD actions from generation.
     *
     * @param string ...$actions Actions to exclude (index, create, edit, show)
     */
    public function without(string ...$actions): static
    {
        $this->actions = array_values(array_diff($this->actions, $actions));

        return $this;
    }

    /**
     * Set explicit column list for index table.
     *
     * @param string[] $columns
     */
    public function columns(array $columns): static
    {
        $this->columnsList = $columns;

        return $this;
    }

    /**
     * Configure a specific column via callback.
     *
     * The callback receives the column descriptor array by reference.
     */
    public function column(string $name, Closure $configurator): static
    {
        $this->columnConfigs[$name] = $configurator;

        return $this;
    }

    /**
     * Set row-level actions for index table.
     *
     * @param string[] $actions
     */
    public function rowActions(array $actions): static
    {
        $this->rowActionsList = $actions;

        return $this;
    }

    /**
     * Set bulk actions for index table.
     *
     * @param string[] $actions
     */
    public function bulkActions(array $actions): static
    {
        $this->bulkActionsList = $actions;

        return $this;
    }

    /**
     * Set page-level actions.
     *
     * @param ActionInterface[] $actions
     */
    public function pageActions(array $actions): static
    {
        $this->pageActionsList = $actions;

        return $this;
    }

    /**
     * Set the form class for create/edit actions.
     *
     * Reserved for future use — stores no state yet.
     */
    public function form(string $formClass): static
    {
        return $this;
    }

    /**
     * Set items per page for index table.
     */
    public function perPage(int $count): static
    {
        $this->perPage = $count;

        return $this;
    }

    /**
     * Set default sort column and direction.
     */
    public function sort(string $column, string $direction = 'desc'): static
    {
        $this->sortColumn = $column;
        $this->sortDirection = $direction;

        return $this;
    }

    /**
     * Opt into i18n for the generated titles.
     *
     * The entity noun is emitted as an i18n intent in `$domain` (keys derived
     * by convention: `<singular>` and `<singular>_plural`). The create/edit
     * verbs are emitted in `$verbs` — a shared UI domain the client layer owns
     * (keys `crud_create`/`crud_edit` with an `entity` placeholder). Without
     * this call the titles fall back to literal singular strings (no verb).
     */
    public function i18n(string $domain, string $verbs = self::VERB_DOMAIN): static
    {
        $this->i18nDomain = $domain;
        $this->verbDomain = $verbs;

        return $this;
    }

    /**
     * Override the entity label (singular and, optionally, plural).
     *
     * Plural resolution: the explicit `$plural` when given; else, when the
     * singular is an i18n intent, the `<key>_plural` convention in the same
     * domain; else the singular itself (no fabricated plural).
     */
    public function label(string|Translatable $singular, string|Translatable|null $plural = null): static
    {
        $this->labelSingular = $singular;
        $this->labelPlural = $plural;

        return $this;
    }

    /**
     * Override the layout template.
     */
    public function layout(string $template): static
    {
        $this->customLayout = $template;

        return $this;
    }

    /**
     * Set a required capability.
     *
     * Reserved for future use — stores no state yet.
     */
    public function capability(string $cap): static
    {
        return $this;
    }

    // --- Build ---

    /**
     * Build PageContract for the given CRUD action.
     *
     * @param string $action One of: index, create, edit, show
     * @param array  $data   Context data (e.g. entity instance for edit/show, rows for index)
     */
    public function build(string $action = 'index', array $data = []): PageContract
    {
        return match ($action) {
            'index' => $this->buildIndex($data),
            'create' => $this->buildCreate($data),
            'edit' => $this->buildEdit($data),
            'show' => $this->buildShow($data),
            default => throw new InvalidArgumentException('Unknown CRUD action: ' . $action),
        };
    }

    /**
     * Check if a given action is enabled.
     */
    public function hasAction(string $action): bool
    {
        return in_array($action, $this->actions, true);
    }

    /**
     * Get the entity slug.
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Resolve the default page title for a CRUD action.
     *
     * index → plural noun; show → singular noun; create/edit → the verb intent
     * (i18n) or, in literal fallback, the singular noun without a verb.
     */
    private function actionTitle(string $action): string|Translatable
    {
        return match ($action) {
            'index' => $this->pluralLabel(),
            'create', 'edit' => $this->verbTitle($action),
            default => $this->singularLabel(),
        };
    }

    /** Resolve the singular entity label: explicit override, i18n intent, or literal. */
    private function singularLabel(): string|Translatable
    {
        if ($this->labelSingular !== null) {
            return $this->labelSingular;
        }

        if ($this->i18nDomain !== null) {
            return Translatable::of($this->singular, $this->i18nDomain);
        }

        return ucfirst($this->singular);
    }

    /** Resolve the plural entity label: explicit, `<key>_plural` i18n intent, or singular fallback. */
    private function pluralLabel(): string|Translatable
    {
        if ($this->labelPlural !== null) {
            return $this->labelPlural;
        }

        $singular = $this->singularLabel();

        if ($singular instanceof Translatable) {
            return Translatable::of($singular->key . '_plural', $singular->domain, $singular->params);
        }

        return $singular;
    }

    /**
     * Resolve a verb-framed title (create/edit).
     *
     * With an i18n singular, emits `crud_<action>` in the verb domain with the
     * entity intent as the `entity` placeholder; otherwise falls back to the
     * literal singular noun (no fabricated English verb).
     */
    private function verbTitle(string $action): string|Translatable
    {
        $singular = $this->singularLabel();

        if ($singular instanceof Translatable) {
            return Translatable::of('crud_' . $action, $this->verbDomain, ['entity' => $singular]);
        }

        return $singular;
    }

    private function buildIndex(array $data): PageContract
    {
        $title = $this->actionTitle('index');
        $columns = $this->columnsList ?? ['name', 'status', 'created_at'];
        $row_actions = $this->rowActionsList ?? ['edit', 'delete'];
        $page_actions = $this->pageActionsList ?? [
            new Action(
                id: 'create',
                label: 'Create',
                target: ActionTarget::link(sprintf('/%s/create', $this->slug)),
                intent: ActionIntent::PRIMARY,
            ),
        ];

        $pagination = $data['pagination'] ?? Pagination::of(1, $this->perPage, 0);

        $table_config = new TableConfig(
            columns: $this->buildColumns($columns),
            rowActions: array_map(fn (string $a): Action => $this->buildRowAction($a), $row_actions),
            bulkActions: $this->bulkActionsList !== null
                ? array_map(fn (string $a): Action => $this->buildBulkAction($a), $this->bulkActionsList)
                : [],
            options: new TableOptions(
                perPage: $this->perPage,
                sortColumn: $this->sortColumn,
                sortDirection: $this->sortDirection,
                selectable: $this->bulkActionsList !== null,
            ),
        );

        $table_data = array_merge(
            $table_config->jsonSerialize(),
            [
                'rows' => $data['rows'] ?? [],
                'pagination' => $pagination instanceof Pagination ? $pagination->jsonSerialize() : $pagination,
            ],
        );

        return PageBuilder::page($this->slug . '.index')
            ->title($title)
            ->layout($this->customLayout ?? 'stack')
            ->actions($page_actions)
            ->region('content', [
                new BlockDescriptor(type: 'dense_table', key: $this->slug . '.table', data: $table_data),
            ])
            ->build();
    }

    private function buildCreate(array $data): PageContract
    {
        $title = $this->actionTitle('create');

        $form_data = [
            'action' => '/' . $this->slug,
            'method' => 'post',
            'schema' => $data['schema'] ?? [],
            'values' => $data['values'] ?? [],
            'errors' => $data['errors'] ?? [],
            'meta' => [
                'multiStep' => false,
                'cancelHref' => '/' . $this->slug,
            ],
        ];

        return PageBuilder::page($this->slug . '.create')
            ->title($title)
            ->layout($this->customLayout ?? 'stack')
            ->region('content', [
                new BlockDescriptor(type: 'form_panel', key: $this->slug . '.form', data: $form_data),
            ])
            ->build();
    }

    private function buildEdit(array $data): PageContract
    {
        $id = $data['id'] ?? 0;
        $title = $this->actionTitle('edit');

        $form_data = [
            'action' => sprintf('/%s/%s', $this->slug, $id),
            'method' => 'put',
            'schema' => $data['schema'] ?? [],
            'values' => $data['values'] ?? [],
            'errors' => $data['errors'] ?? [],
            'meta' => [
                'multiStep' => false,
                'cancelHref' => '/' . $this->slug,
            ],
        ];

        return PageBuilder::page($this->slug . '.edit')
            ->title($title)
            ->layout($this->customLayout ?? 'stack')
            ->region('content', [
                new BlockDescriptor(type: 'form_panel', key: $this->slug . '.form', data: $form_data),
            ])
            ->build();
    }

    private function buildShow(array $data): PageContract
    {
        $title = $this->actionTitle('show');

        return PageBuilder::page($this->slug . '.show')
            ->title($title)
            ->layout($this->customLayout ?? 'split')
            ->region('content', [
                new BlockDescriptor(type: 'detail_panel', key: $this->slug . '.detail', data: $data['detail'] ?? []),
            ])
            ->region('aside', [
                new BlockDescriptor(type: 'activity_timeline', key: $this->slug . '.activity', data: $data['activity'] ?? []),
            ])
            ->build();
    }

    /**
     * @param string[] $columns
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildColumnDescriptors(array $columns): array
    {
        return array_map(function (string $col): array {
            $descriptor = [
                'key' => $col,
                'label' => ucfirst(str_replace('_', ' ', $col)),
                'sortable' => in_array($col, ['name', 'created_at', 'updated_at', 'status'], true),
            ];

            if (isset($this->columnConfigs[$col])) {
                ($this->columnConfigs[$col])($descriptor);
            }

            return $descriptor;
        }, $columns);
    }

    /**
     * Convert convention column descriptors into typed Column VOs.
     *
     * @param string[] $columns
     *
     * @return Column[]
     */
    private function buildColumns(array $columns): array
    {
        return array_map(
            static fn (array $d): Column => new Column(
                key: $d['key'],
                label: $d['label'] ?? '',
                sortable: $d['sortable'] ?? false,
                searchable: $d['searchable'] ?? false,
                format: $d['format'] ?? ValueFormat::TEXT,
                formatOptions: $d['formatOptions'] ?? [],
                options: $d['options'] ?? [],
            ),
            $this->buildColumnDescriptors($columns),
        );
    }

    /**
     * Derive a typed row Action from a convention name.
     *
     * Conventions: `delete` → DELETE request (+ confirmation); `show` → link to
     * the entity; anything else → link to `/{slug}/{id}/{action}`.
     */
    private function buildRowAction(string $action): Action
    {
        $label = ucfirst($action);

        return match ($action) {
            'delete' => new Action(
                id: 'delete',
                label: $label,
                target: ActionTarget::request(sprintf('/%s/{id}', $this->slug), HttpMethod::DELETE),
                intent: ActionIntent::DANGER,
                confirmation: new Confirmation(title: 'Delete', message: 'Are you sure?', variant: 'danger'),
            ),
            'show' => new Action(
                id: 'show',
                label: $label,
                target: ActionTarget::link(sprintf('/%s/{id}', $this->slug)),
            ),
            default => new Action(
                id: $action,
                label: $label,
                target: ActionTarget::link(sprintf('/%s/{id}/%s', $this->slug, $action)),
            ),
        };
    }

    /**
     * Derive a typed bulk Action from a convention name.
     *
     * Bulk actions POST to `/{slug}/bulk/{action}`; `delete` is DANGER + confirmed.
     */
    private function buildBulkAction(string $action): Action
    {
        return new Action(
            id: $action,
            label: ucfirst($action),
            target: ActionTarget::request(sprintf('/%s/bulk/%s', $this->slug, $action)),
            intent: $action === 'delete' ? ActionIntent::DANGER : ActionIntent::SECONDARY,
            confirmation: $action === 'delete'
                ? new Confirmation(title: 'Delete', message: 'Are you sure?', variant: 'danger')
                : null,
        );
    }
}
