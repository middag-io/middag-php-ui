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
use Middag\Ui\Contract\CrudBuilderInterface;
use Middag\Ui\Contract\PageActionInterface;
use Middag\Ui\Data\BlockDescriptor;
use Middag\Ui\Data\PageAction;
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
    private readonly string $slug;

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

    /** @var null|PageActionInterface[] Page actions (null = default ['create']) */
    private ?array $pageActionsList = null;

    private int $perPage = 25;

    private string $sortColumn = 'created_at';

    private string $sortDirection = 'desc';

    private ?string $customTitle = null;

    private ?string $customLayout = null;

    private function __construct(private readonly string $entity_class)
    {
        // Derive slug from class name: Segment -> segments.
        $parts = explode('\\', $this->entity_class);
        $basename = end($parts);
        $this->slug = strtolower($basename) . 's';
    }

    public static function for(string $entity_class): self
    {
        return new self($entity_class);
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
     * @param PageActionInterface[] $actions
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
    public function form(string $form_class): static
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
     * Override the page title.
     */
    public function title(string $title): static
    {
        $this->customTitle = $title;

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

    private function buildIndex(array $data): PageContract
    {
        $title = $this->customTitle ?? ucfirst($this->slug);
        $columns = $this->columnsList ?? ['name', 'status', 'created_at'];
        $row_actions = $this->rowActionsList ?? ['edit', 'delete'];
        $page_actions = $this->pageActionsList ?? [
            new PageAction(id: 'create', label: 'Create', intent: 'primary', href: sprintf('/%s/create', $this->slug)),
        ];

        $table_data = [
            'columns' => $this->buildColumnDescriptors($columns),
            'rows' => $data['rows'] ?? [],
            'pagination' => $data['pagination'] ?? [
                'page' => 1,
                'perPage' => $this->perPage,
                'total' => 0,
                'lastPage' => 1,
            ],
            'sort' => [
                'column' => $this->sortColumn,
                'direction' => $this->sortDirection,
            ],
            'rowActions' => array_map(
                fn (string $a): array => ['id' => $a, 'label' => ucfirst($a)],
                $row_actions,
            ),
            'bulkActions' => $this->bulkActionsList !== null
                ? array_map(fn (string $a): array => ['id' => $a, 'label' => ucfirst($a)], $this->bulkActionsList)
                : [],
        ];

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
        $title = $this->customTitle
            ? 'Create ' . $this->customTitle
            : 'Create ' . rtrim(ucfirst($this->slug), 's');

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
        $title = $this->customTitle
            ? 'Edit ' . $this->customTitle
            : 'Edit ' . rtrim(ucfirst($this->slug), 's');

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
        $title = $this->customTitle ?? rtrim(ucfirst($this->slug), 's');

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
}
